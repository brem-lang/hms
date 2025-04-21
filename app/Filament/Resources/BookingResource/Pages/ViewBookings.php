<?php

namespace App\Filament\Resources\BookingResource\Pages;

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

class ViewBookings extends Page
{
    public $record;

    public ?array $formData = [];

    protected static string $resource = BookingResource::class;

    protected static string $view = 'filament.resources.booking-resource.pages.view-bookings';

    public function mount(Booking $record): void
    {
        $this->form->fill([
            'proof_of_payment' => $record->proof_of_payment,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('proof_of_payment')
                    ->openable()
                    ->columnSpanFull()
                    ->label('Proof of Payment')
                    ->required()
                    ->disk('public_uploads_payment')
                    ->directory('/')
                    ->hint('Please upload the proof of payment for gcash.'),
            ])
            ->columns(2)
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
                TextEntry::make('start_date')->dateTime()->label('Start Date'),
                TextEntry::make('end_date')->dateTime()->label('End Date'),
                TextEntry::make('created_at')->dateTime()->label('Date of Booking'),
                TextEntry::make('days')->label('Days'),
                TextEntry::make('duration')->label('Duration Hrs'),
                TextEntry::make('no_persons')->label('Number of Persons'),
                TextEntry::make('check_in_date')->dateTime()->label('Check In Time')
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)->format('F j, Y h:i A');
                    }),
                TextEntry::make('check_out_date')->dateTime()->label('Check Out Time')
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)->format('F j, Y h:i A');
                    }),
                TextEntry::make('amount_to_pay')->label('Payment')->prefix('₱ '),
                TextEntry::make('room.name')->label('Suite Type'),
                TextEntry::make('suiteRoom.name')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->label('Room'),
                TextEntry::make('type')->label('Booking Type')
                    ->formatStateUsing(function ($state) {
                        return $state === 'walkin_booking' ? 'Walk-in' : 'Online';
                    }),
            ])
            ->columns(3);
    }

    public function confirm()
    {
        $this->record->status = 'completed';

        $this->record->save();

        Notification::make()
            ->success()
            ->title('Booking Confirmed')
            ->icon('heroicon-o-check-circle')
            ->send();

        Notification::make()
            ->success()
            ->title('Payment Confirmed')
            ->icon('heroicon-o-check-circle')
            ->body($this->record->user->name.' your booking has been confirmed')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn () => MyBookingResource::getUrl('payment', ['record' => $this->record->id])),
                // ->openUrlInNewTab()
            ])
            ->sendToDatabase(User::where('id', $this->record->user_id)->get());

        // $this->dispatch('close-modal', id: 'confirm-modal');

        redirect(BookingResource::getUrl('view', ['record' => $this->record->id]));
    }
}
