<?php

namespace App\Livewire;

use App\Filament\Resources\BookingResource;
use App\Mail\MailFrontDesk;
use App\Models\Booking;
use App\Models\Charge;
use App\Models\Food;
use App\Models\Room;
use App\Models\SuiteRoom;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ViewBooking extends Component implements HasForms
{
    use InteractsWithForms;

    public Booking $booking;

    public ?array $formData = [];

    public $reason;

    public $charges = [];

    public $foodCharges = [];

    public $rebook_check_in_date;

    public $rebook_check_out_date;

    public $rebook_notes;

    public function canRebook()
    {
        if (! $this->booking->check_in_date) {
            return false;
        }

        $checkInDate = is_string($this->booking->check_in_date)
            ? Carbon::parse($this->booking->check_in_date)
            : $this->booking->check_in_date;

        // Check if current date is at least 2 days before check-in date
        return now()->diffInDays($checkInDate, false) >= 2;
    }

    public function render()
    {
        return view('livewire.view-booking');
    }

    public function mount($id)
    {
        $this->booking = Booking::with('user', 'room', 'suiteRoom', 'walkingGuest', 'relatedBookings')->find($id);

        $this->authorize('view', $this->booking);

        $chargeIds = collect($this->booking->additional_charges)->pluck('name')->unique();
        $foodchargeId = collect($this->booking->food_charges)->pluck('name')->unique();
        $this->charges = Charge::whereIn('id', $chargeIds)->pluck('name', 'id');
        $this->foodCharges = Food::whereIn('id', $foodchargeId)->pluck('name', 'id');

        $this->form->fill([
            'proof_of_payment' => $this->booking->proof_of_payment,
        ]);

        // Handle check_in_date - convert to Carbon if it's a string
        $checkInDate = $this->booking->check_in_date;
        if ($checkInDate) {
            $checkInDate = is_string($checkInDate) ? Carbon::parse($checkInDate) : $checkInDate;
            $this->rebook_check_in_date = $checkInDate->format('Y-m-d\TH:i');
        }

        // Handle check_out_date - convert to Carbon if it's a string
        $checkOutDate = $this->booking->check_out_date;
        if ($checkOutDate) {
            $checkOutDate = is_string($checkOutDate) ? Carbon::parse($checkOutDate) : $checkOutDate;
            $this->rebook_check_out_date = $checkOutDate->format('Y-m-d\TH:i');
        }

        $this->rebook_notes = $this->booking->rebook_notes ?? '';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('proof_of_payment')
                    ->required()
                    ->label('Proof of Payment')
                    ->openable()
                    ->disk('public_uploads_payment')
                    ->directory('/')
                    ->image()
                    ->hint('You may pay 50% of the total amount. Please upload your GCash payment proof. Kindly complete your payment within 2 hours to confirm your reservation.
                    '),
            ])
            ->statePath('formData');
    }

    public function pay()
    {
        if ($this->booking->created_at->diffInHours(now()) > 2) {
            $this->dispatch('swal:success', [
                'title' => 'The payment deadline has passed, and your booking has been automatically cancelled.',
                'icon' => 'error',
            ]);

            $this->booking->status = 'cancelled';
            $this->booking->save();

            return;
        }

        $data = $this->form->getState();

        $this->booking->proof_of_payment = $data['proof_of_payment'];

        $this->booking->is_proof_send = true;

        $this->booking->relatedBookings()->update([
            'proof_of_payment' => $data['proof_of_payment'],
            'is_proof_send' => true,
        ]);

        $this->booking->save();

        $this->dispatch('swal:success', [
            'title' => 'Thank you! Your booking is confirmed. We look forward to serving you.',
            'icon' => 'success',
        ]);

        Notification::make()
            ->success()
            ->title('Payment Sent')
            ->icon('heroicon-o-check-circle')
            ->body(auth()->user()->name.' has sent payment')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn () => BookingResource::getUrl('view', ['record' => $this->booking->id]))
                    ->markAsRead(),
            ])
            ->sendToDatabase(User::where('role', '!=', 'customer')->get());

        // redirect('/view-booking/'.$this->booking->id);
    }

    public function cancel()
    {
        $frontDesk = User::where('role', 'front-desk')->first();

        $details = [
            'name' => $frontDesk->name,
            'message' => $this->reason,
            'user' => auth()->user()->name,
            'type' => 'mail_from_user',
        ];

        Mail::to($frontDesk->email)->send(new MailFrontDesk($details));

        $this->reason = '';

        $this->dispatch('close-modal', id: 'mail-modal');

        $this->dispatch('swal:success', [
            'title' => 'Your EMail has been sent to the front desk. We will process it shortly.',
            'icon' => 'success',
        ]);

        Notification::make()
            ->success()
            ->title('Received Email Request')
            ->icon('heroicon-o-check-circle')
            ->body(auth()->user()->name.' has sent an email request')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn () => BookingResource::getUrl('view', ['record' => $this->booking->id]))
                    ->markAsRead(),
            ])
            ->sendToDatabase(User::where('role', '!=', 'customer')->get());

        // return redirect('/my-bookings');
    }

    public function getFunctionHallTime($checkIn, $checkOut, $suiteRoomId)
    {
        $newCheckInDate = Carbon::parse($checkIn);
        $newCheckOutDate = Carbon::parse($checkOut);

        $bookings = Booking::where('suite_room_id', $suiteRoomId)
            ->where('room_id', 4)
            ->where('status', 'completed')
            ->where('type', '!=', 'bulk_head_online')
            ->where('id', '!=', $this->booking->id)
            ->whereDate('check_in_date', '<=', $newCheckInDate)
            ->whereDate('check_out_date', '>=', $newCheckOutDate)
            ->get();

        if ($bookings->count() >= 2) {
            return false;
        }

        $checkInDateOnly = Carbon::parse($checkIn)->toDateString();

        $precedingBooking = Booking::where('suite_room_id', $suiteRoomId)
            ->where('room_id', 4)
            ->where('status', 'completed')
            ->where('type', '!=', 'bulk_head_online')
            ->where('id', '!=', $this->booking->id)
            ->whereDate('check_in_date', '<=', $checkInDateOnly)
            ->whereDate('check_out_date', '>=', $checkInDateOnly)
            ->first();

        if ($precedingBooking) {
            $precedingBookingCheckOut = Carbon::parse($precedingBooking->check_out_date);
            $proposedCheckIn = Carbon::parse($checkIn);
            $requiredStartBuffer = $precedingBookingCheckOut->copy()->addHours(2);
            if ($proposedCheckIn->lessThan($requiredStartBuffer)) {
                return true;
            } else {
                return null;
            }
        }
    }

    public function getSuiteRoom($suiteID, $checkIn, $checkOut)
    {
        $bookedRoomIds = Booking::where('status', '!=', 'cancelled')
            ->where('status', '!=', 'done')
            ->where('type', '!=', 'bulk_head_online')
            ->where('id', '!=', $this->booking->id)
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where('check_in_date', '<', $checkOut)
                    ->where('check_out_date', '>', $checkIn);
            })
            ->pluck('suite_room_id');

        $availableRoom = SuiteRoom::where('room_id', $suiteID)
            ->where('is_active', true)
            ->where('is_occupied', false)
            ->whereNotIn('id', $bookedRoomIds)
            ->first();

        return $availableRoom?->id ?? false;
    }

    public function rebook()
    {
        // Check if rebooking is allowed (at least 2 days before check-in)
        if (! $this->canRebook()) {
            $this->dispatch('swal:success', [
                'title' => 'Rebooking is only available at least 2 days before your check-in date.',
                'icon' => 'error',
            ]);

            return;
        }

        $this->validate([
            'rebook_check_in_date' => 'required|date',
            'rebook_check_out_date' => 'required|date|after:rebook_check_in_date',
            'rebook_notes' => 'required|string|max:255',
        ]);

        $checkInDate = Carbon::parse($this->rebook_check_in_date);
        $checkOutDate = Carbon::parse($this->rebook_check_out_date);

        // Convert booking dates to Carbon if they're strings
        $bookingCheckIn = is_string($this->booking->check_in_date)
            ? Carbon::parse($this->booking->check_in_date)
            : $this->booking->check_in_date;

        $bookingCheckOut = is_string($this->booking->check_out_date)
            ? Carbon::parse($this->booking->check_out_date)
            : $this->booking->check_out_date;

        // Check if dates are different
        if (
            $bookingCheckIn->format('Y-m-d H:i:s') == $checkInDate->format('Y-m-d H:i:s') &&
            $bookingCheckOut->format('Y-m-d H:i:s') == $checkOutDate->format('Y-m-d H:i:s')
        ) {
            $this->dispatch('swal:success', [
                'title' => 'Please select different dates for rebooking',
                'icon' => 'error',
            ]);

            return;
        }

        // Check room availability
        if ($this->booking->room_id != 4) {
            $room = $this->getSuiteRoom($this->booking->room_id, $checkInDate->toDateTimeString(), $checkOutDate->toDateTimeString());

            if (! $room) {
                $this->dispatch('swal:success', [
                    'title' => 'Please select different dates for rebooking',
                    'icon' => 'error',
                ]);

                return;
            }

            $this->booking->suite_room_id = $room;
        } else {
            if ($this->getFunctionHallTime($checkInDate->toDateTimeString(), $checkOutDate->toDateTimeString(), $this->booking->suite_room_id) === false) {
                $this->dispatch('swal:success', [
                    'title' => 'Function Hall is fully booked',
                    'icon' => 'error',
                ]);

                return;
            }

            if ($this->getFunctionHallTime($checkInDate->toDateTimeString(), $checkOutDate->toDateTimeString(), $this->booking->suite_room_id)) {
                $this->dispatch('swal:success', [
                    'title' => 'The selected time is not available',
                    'icon' => 'error',
                ]);

                return;
            }
        }

        // Update booking dates
        $this->booking->check_in_date = $checkInDate;
        $this->booking->check_out_date = $checkOutDate;
        $this->booking->rebook_notes = $this->rebook_notes;
        $this->booking->save();

        $this->dispatch('close-modal', id: 'rebook-modal');

        $this->dispatch('swal:success', [
            'title' => 'Booking updated successfully!',
            'icon' => 'success',
        ]);

        Notification::make()
            ->success()
            ->title('Booking Updated')
            ->icon('heroicon-o-check-circle')
            ->body(auth()->user()->name.' has rebooked your booking')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn () => BookingResource::getUrl('view', ['record' => $this->booking->id]))
                    ->markAsRead(),
            ])
            ->sendToDatabase(User::where('role', '!=', 'customer')->get());
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->to('/');
    }
}
