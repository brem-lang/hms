<?php

namespace App\Livewire;

use App\Filament\Resources\BookingResource;
use App\Mail\MailFrontDesk;
use App\Models\Booking;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class ViewBooking extends Component implements HasForms
{
    use InteractsWithForms;

    public Booking $booking;

    public ?array $formData = [];

    public $reason;

    public function render()
    {
        return view('livewire.view-booking');
    }

    public function mount($id)
    {
        $this->booking = Booking::with('user', 'room', 'suiteRoom', 'walkingGuest', 'relatedBookings')->find($id);

        $this->authorize('view', $this->booking);

        $this->form->fill([
            'proof_of_payment' => $this->booking->proof_of_payment,
        ]);
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
            'title' => 'Your cancellation request has been sent to the front desk. We will process it shortly.',
            'icon' => 'success',
        ]);

        // return redirect('/my-bookings');
    }
}
