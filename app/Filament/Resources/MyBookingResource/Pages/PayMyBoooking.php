<?php

namespace App\Filament\Resources\MyBookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\MyBookingResource;
use App\Models\Booking;
use App\Models\User;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Actions\Action as NotificationsAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;

class PayMyBoooking extends Page
{
    protected static string $resource = MyBookingResource::class;

    public $record;

    public ?array $formData = [];

    protected static string $view = 'filament.resources.my-booking-resource.pages.pay-my-boooking';

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         ActionsAction::make('cancel_booking')
    //             ->label('Cancel Booking')
    //             ->action(function ($data) {
    //                 $this->record->want_cancel = true;
    //                 $this->record->cancel_reason = $data['cancel_reason'];

    //                 $this->record->save();

    //                 Notification::make()
    //                     ->title('Booking Cancelled')
    //                     ->body('Wait for the admin to approve your cancellation')
    //                     ->success()
    //                     ->icon('heroicon-o-check-circle')
    //                     ->send();

    //                 Notification::make()
    //                     ->title(auth()->user()->name.',wants to cancel booking')
    //                     ->success()
    //                     ->actions([
    //                         NotificationsAction::make('view')
    //                             ->label('View')
    //                             ->url(fn () => BookingResource::getUrl('view', ['record' => $this->record->id]))
    //                             ->markAsRead(),
    //                     ])
    //                     ->sendToDatabase(User::whereIn('role', ['supervisor', 'front-desk'])->get());
    //             })
    //             ->color('danger')
    //             ->visible(fn () => $this->record->status === 'completed')
    //             ->hidden(fn () => $this->record->want_cancel)
    //             ->form([
    //                 Textarea::make('cancel_reason')
    //                     ->label('Reason for Cancellation')
    //                     ->required()
    //                     ->maxLength(255)
    //                     ->placeholder('Please provide a reason for cancellation'),
    //             ])
    //             ->modalWidth('lg')
    //             ->icon('heroicon-o-x-circle'),
    //         ActionsAction::make('cancel_final')
    //             ->label('Cancel Booking')
    //             ->action(function ($data) {
    //                 $this->record->status = 'cancelled';
    //                 $this->record->save();

    //                 Notification::make()
    //                     ->success()
    //                     ->title('Booking Cancelled')
    //                     ->icon('heroicon-o-check-circle')
    //                     ->send();
    //             })
    //             ->color('danger')
    //             ->visible(fn () => $this->record->can_cancel)
    //             ->hidden(fn () => $this->record->status === 'cancelled')
    //             ->requiresConfirmation()
    //             ->modalWidth('lg')
    //             ->icon('heroicon-o-x-circle'),
    //     ];
    // }

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
                    ->disk('public_uploads_payment')
                    ->directory('/')
                    ->image()
                    ->hint('You can pay 50% || Please upload the proof of payment for gcash.'),
            ])
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
                        'done' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => __(ucfirst($state))),
                TextEntry::make('start_date')->dateTime()->hidden(function ($record) {
                    return $record->room_id == 4;
                })->label('Start Date'),
                TextEntry::make('end_date')
                    ->hidden(function ($record) {
                        return $record->room_id == 4;
                    })->dateTime()->label('End Date'),
                TextEntry::make('created_at')->dateTime()->label('Date of Booking')->formatStateUsing(function ($state) {
                    return \Carbon\Carbon::parse($state)->timezone('Asia/Manila')->format('F j, Y h:i A');
                }),
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
                TextEntry::make('amount_to_pay')->label('Amount')->prefix('₱ '),
                TextEntry::make('amount_paid')->label('Amount Paid')->prefix('₱ '),
                TextEntry::make('balance')->label('Balance')->prefix('₱ '),
                TextEntry::make('room.name')->label('Suite Type'),
                TextEntry::make('suiteRoom.name')
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->label('Room'),
            ])
            ->columns(3);
    }

    public function notesInfoList(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                TextEntry::make('notes'),
            ])
            ->columns(1);
    }

    public function pay()
    {
        $data = $this->form->getState();

        $this->record->proof_of_payment = $data['proof_of_payment'];

        $this->record->is_proof_send = true;

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
            ->body(auth()->user()->name.' has sent payment')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn () => BookingResource::getUrl('view', ['record' => $this->record->id]))
                    ->markAsRead(),
            ])
            ->sendToDatabase(User::where('role', '!=', 'customer')->get());

        // $this->dispatch('close-modal', id: 'confirm-modal');

        redirect(MyBookingResource::getUrl('payment', ['record' => $this->record->id]));
    }
}
