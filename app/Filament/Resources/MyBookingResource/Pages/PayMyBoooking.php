<?php

namespace App\Filament\Resources\MyBookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\MyBookingResource;
use App\Models\Booking;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class PayMyBoooking extends Page
{
    protected static string $resource = MyBookingResource::class;

    public $record;

    public ?array $formData = [];

    protected static string $view = 'filament.resources.my-booking-resource.pages.pay-my-boooking';

    public function mount(Booking $record): void
    {
        // if ($this->record->status != 'pending') {
        //     abort(404);
        // }

        $this->form->fill([
            'proof_of_payment' => $record->proof_of_payment,
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
                    ->disk('public')
                    ->directory('payments')
                    ->image()
                    ->hint('Please upload the proof of payment for gcash.'),
            ])
            // ->columns(1)
            ->statePath('formData');
    }

    public function infoList(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                TextEntry::make('user.name'),
                TextEntry::make('user.contact_number')
                    ->label('Contact Number'),
                TextEntry::make('status')
                    ->label('')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => __(ucfirst($state))),
                TextEntry::make('start_date')->label('Start Date')
                    ->dateTime(),
                TextEntry::make('end_date')->label('End Date')
                    ->dateTime(),
                TextEntry::make('duration')->label('Duration Hrs'),
                TextEntry::make('created_at')->dateTime()->label('Date of Booking'),
                TextEntry::make('amount_to_pay')->label('Payment')->prefix('₱ '),
            ])
            ->columns(3);
    }

    public function pay()
    {
        $data = $this->form->getState();

        $this->record->proof_of_payment = $data['proof_of_payment'];

        $this->record->save();

        Notification::make()
            ->success()
            ->title('Payment Sent')
            ->icon('heroicon-o-check-circle')
            ->send();

        Notification::make()
            ->success()
            ->title('Payment Sent')
            ->icon('heroicon-o-check-circle')
            ->body(auth()->user()->name.' has sent '.$this->record->amount_to_pay)
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn () => BookingResource::getUrl('view', ['record' => $this->record->id])),
            ])
            ->sendToDatabase(User::where('role', '!=', 'customer')->get());

        $this->dispatch('close-modal', id: 'confirm-modal');
    }
}
