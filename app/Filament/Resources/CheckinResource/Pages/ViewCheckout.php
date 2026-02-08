<?php

namespace App\Filament\Resources\CheckinResource\Pages;

use App\Filament\Resources\CheckinResource;
use App\Mail\MailFrontDesk;
use App\Models\Booking;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Mail;

class ViewCheckout extends Page
{
    public $record;

    protected static string $resource = CheckinResource::class;

    protected static string $view = 'filament.resources.checkin-resource.pages.view-checkout';

    public function getTitle(): string
    {
        return 'Check-Out Details - '.$this->record->booking_number;
    }

    public function mount(Booking $record): void
    {
        $this->record = $record;
    }

    public function checkOut()
    {
        $chargesAmount = 0;
        foreach ($this->record->additional_charges ?? [] as $charge) {
            $chargesAmount += $charge['total_charges'];
        }

        $foodChargesAmount = 0;
        foreach ($this->record->food_charges ?? [] as $charge) {
            $foodChargesAmount += $charge['total_charges'];
        }

        $this->record->status = 'done';
        $this->record->is_occupied = 0;
        $this->record->balance = 0;
        $this->record->amount_paid = $this->record->amount_to_pay + $chargesAmount + $foodChargesAmount;
        $this->record->amount_to_pay = $this->record->amount_to_pay + $chargesAmount + $foodChargesAmount;
        $this->record->suiteRoom->is_occupied = 0;
        $this->record->suiteRoom->save();
        $this->record->save();

        if ($this->record->getBookingHead) {
            $this->record->getBookingHead->update([
                'status' => 'done',
            ]);
        }

        if ($this->record->room_id != 4) {
            $details = [
                'name' => $this->record->user->name,
                'message' => 'You have been checked out successfully. Thank you for choosing us!',
                'amount_paid' => $this->record->amount_paid ?? 0,
                'balance' => $this->record->balance ?? 0,
                'type' => 'check_out',
            ];

            Mail::to($this->record->type == 'online' ? $this->record->user->email : $this->record->walkingGuest->email)->send(new MailFrontDesk($details));
        } else {
            $details = [
                'name' => $this->record->organization.' '.$this->record->position,
                'message' => 'You have been checked out successfully. Thank you for choosing us!',
                'amount_paid' => $this->record->amount_paid ?? 0,
                'balance' => $this->record->balance ?? 0,
                'type' => 'check_out',
            ];
            Mail::to($this->record->email)->send(new MailFrontDesk($details));
        }

        Notification::make()
            ->success()
            ->title('Check Out')
            ->send();

        return redirect(CheckinResource::getUrl('index'));
    }
}
