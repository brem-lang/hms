<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\MyBookingResource;
use App\Mail\MailFrontDesk;
use App\Models\Booking;
use App\Models\Charge;
use App\Models\User;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Mail;

class ViewBookings extends Page
{
    public $record;

    public ?array $formData = [];

    public ?array $cancelData = [];

    protected static string $resource = BookingResource::class;

    protected static string $view = 'filament.resources.booking-resource.pages.view-bookings';

    public function getTitle(): string
    {
        return 'View Booking - '.$this->record->booking_number;
    }

    public function mount(Booking $record): void
    {
        $this->paymentForm->fill([
            'proof_of_payment' => $record->proof_of_payment,
            'payment_type' => $record->payment_type ?? 'cash',
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [

            ActionsAction::make('additional_charges')
                ->label('Charges')
                ->icon('heroicon-o-plus-circle')
                ->form([
                    Repeater::make('charges')
                        ->formatStateUsing(fn () => $this->record->additional_charges)
                        ->label('Additional Charges')
                        ->reorderable(false)
                        ->schema([
                            Select::make('name')
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->required()
                                ->label('Charge Name')
                                ->options(Charge::pluck('name', 'id'))
                                ->live()
                                ->afterStateUpdated(function (Set $set, ?string $state) {
                                    $charge = Charge::find($state);
                                    $set('amount', $charge?->amount);
                                })
                                ->searchable(),

                            TextInput::make('amount')
                                ->label('Amount')
                                ->prefix('PHP')
                                ->numeric()
                                ->readOnly(),
                        ])
                        ->columns(2),
                ])->action(function ($data) {

                    $this->record->additional_charges = $data['charges'];
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Charges Updated')
                        ->icon('heroicon-o-check-circle')
                        ->send();
                })
                ->visible(fn () => $this->record->status === 'completed' && $this->record?->suiteRoom?->is_occupied === 1),

            ActionsAction::make('more_details')
                ->icon('heroicon-o-document-text')
                ->label('Guest Details')
                ->form([
                    TextInput::make('name')
                        ->formatStateUsing(fn () => $this->record->walkingGuest?->first_name.' '.$this->record->walkingGuest?->last_name)
                        ->readOnly(),
                    TextInput::make('email')
                        ->formatStateUsing(fn () => $this->record->walkingGuest?->email)
                        ->readOnly(),
                    TextInput::make('phone')
                        ->formatStateUsing(fn () => $this->record->walkingGuest?->phone)
                        ->readOnly(),
                ])
                ->visible(fn () => $this->record->walkingGuest)
                ->modalCancelAction(false)
                ->modalSubmitAction(false),
            ActionsAction::make('confirm_cancel')
                ->label('Cancel Booking')
                ->action(function ($data) {
                    $this->record->status = 'cancelled';
                    $this->record->cancel_reason = $data['cancel_reason'];
                    $this->record->want_cancel = true;
                    $this->record->save();

                    Notification::make()
                        ->success()
                        ->title('Booking Cancelled')
                        ->icon('heroicon-o-check-circle')
                        ->body($this->record->user->name.' your booking has been cancelled')
                        ->actions([
                            Action::make('view')
                                ->label('View')
                                ->url(fn () => MyBookingResource::getUrl('payment', ['record' => $this->record->id]))->markAsRead()
                                ->markAsRead(),
                        ])
                        ->sendToDatabase(User::where('id', $this->record->user_id)->get());

                    $details = [
                        'name' => $this->record->user->name,
                        'message' => $data['cancel_reason'],
                        'type' => 'cancel_booking',
                    ];

                    Mail::to($this->record->user->email)->send(new MailFrontDesk($details));

                    Notification::make()
                        ->success()
                        ->title('Booking Cancelled')
                        ->icon('heroicon-o-check-circle')
                        ->send();
                })
                ->color('danger')
                ->hidden(true)
                ->form([
                    Textarea::make('cancel_reason')
                        ->label('Reason for Cancellation')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Please provide a reason for cancellation'),
                ])
                ->modalWidth('lg')
                ->icon('heroicon-o-check-circle'),
        ];
    }

    protected function getForms(): array
    {
        return [
            'paymentForm',
            'cancelForm',
        ];
    }

    public function paymentForm(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('payment_type')
                    ->live()
                    ->label('Payment Type')
                    ->options([
                        'gcash' => 'GCash',
                        'cash' => 'Cash',
                    ])
                    ->required(),
                TextInput::make('amount_paid')
                    ->numeric()
                    ->required(),
                FileUpload::make('proof_of_payment')
                    ->openable()
                    ->image()
                    ->columnSpanFull()
                    ->label('Proof of Payment')
                    ->required()
                    ->disk('public_uploads_payment')
                    ->visible(fn ($get) => $get('payment_type') === 'gcash')
                    ->directory('/')
                    ->hint('Please upload the proof of payment for gcash.'),
            ])
            ->columns(2)
            ->statePath('formData');
    }

    public function cancelForm(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('cancel_reason')
                    ->label('Reason')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Please provide a reason'),
            ])
            ->statePath('cancelData');
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
                        'completed' => 'warning',
                        'cancelled' => 'danger',
                        'done' => 'success',
                        'returned' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'For CheckIn',
                        default => __(ucfirst($state)),
                    }),
                TextEntry::make('start_date')->dateTime()->label('Start Date'),
                TextEntry::make('end_date')->dateTime()->label('End Date'),
                TextEntry::make('created_at')->dateTime()->label('Date of Booking')->formatStateUsing(function ($state) {
                    return \Carbon\Carbon::parse($state)->timezone('Asia/Manila')->format('F j, Y h:i A');
                }),
                TextEntry::make('days')->label('Days'),
                TextEntry::make('duration')->label('Duration Hrs'),
                TextEntry::make('no_persons')->label('Number of Persons')
                    ->formatStateUsing(function ($record) {
                        return $record->type != 'bulk_head_online' ? $record->no_persons : $record->relatedBookings->sum('no_persons');
                    }),
                TextEntry::make('check_in_date')->dateTime()->label('Check In Time')
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)->format('F j, Y h:i A');
                    }),
                TextEntry::make('check_out_date')->dateTime()->label('Check Out Time')
                    ->formatStateUsing(function ($state) {
                        return \Carbon\Carbon::parse($state)->format('F j, Y h:i A');
                    }),
                TextEntry::make('amount_to_pay')->label('Amount')->prefix('₱ ')
                    ->formatStateUsing(function ($record) {
                        return $record->type != 'bulk_head_online' ? number_format($record->amount_to_pay, 2) : number_format($record->relatedBookings->sum('amount_to_pay'), 2);
                    }),
                TextEntry::make('amount_paid')->label('Amount Paid')->prefix('₱ '),
                TextEntry::make('balance')->label('Balance')
                    ->formatStateUsing(function ($state, $record) {
                        $chargesAmount = 0;
                        foreach ($record['additional_charges'] ?? [] as $charge) {
                            $chargesAmount += $charge['amount'];
                        }

                        return $state + $chargesAmount;
                    })
                    ->prefix('₱ '),
                TextEntry::make('room.name')->label('Suite Type'),
                TextEntry::make('notes')->label('Notes/Requests'),
                TextEntry::make('created_at')
                    ->formatStateUsing(function ($record) {
                        if ($record->type !== 'bulk_head_online') {
                            return ucfirst($record->suiteRoom->name ?? '');
                        }

                        // Collect all related suiteRoom names
                        $names = $record->relatedBookings
                            ->pluck('suiteRoom.name')
                            ->filter()               // remove nulls
                            ->map('ucfirst')
                            ->implode(', ');

                        return $names;
                    })
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
        $data = $this->paymentForm->getState();
        if ($this->record->type === 'bulk_head_online' && $data['amount_paid'] > $this->record->relatedBookings->sum('amount_to_pay')) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Amount paid is greater than amount to pay')
                ->send();

            return;
        }
        $this->record->amount_paid = $data['amount_paid'];
        $this->record->balance = $this->record->type === 'bulk_head_online' ? $this->record->relatedBookings->sum('amount_to_pay') - $data['amount_paid'] : $this->record->amount_to_pay - $data['amount_paid'];
        $this->record->proof_of_payment = $data['proof_of_payment'] ?? null;
        $this->record->payment_type = $data['payment_type'];

        $this->record->status = 'completed';

        $this->record->save();

        $total = $data['amount_paid'];
        $related = $this->record->relatedBookings;

        if ($related->count() > 0) {
            $share = $total / $related->count();

            foreach ($related as $booking) {
                $booking->update([
                    'amount_paid' => $share,
                    'status' => 'completed',
                    'balance' => $booking->amount_to_pay - $share,
                ]);

                // $booking->suiteRoom->update([
                //     'is_occupied' => 1,
                // ]);
            }
        }

        Notification::make()
            ->success()
            ->title('Booking Confirmed')
            ->icon('heroicon-o-check-circle')
            ->send();

        if (auth()->user()?->role == 'customer') {
            Notification::make()
                ->success()
                ->title('Payment Confirmed')
                ->icon('heroicon-o-check-circle')
                ->body($this->record->user->name.' your booking has been confirmed')
                ->actions([
                    Action::make('view')
                        ->label('View')
                        ->url(fn () => MyBookingResource::getUrl('payment', ['record' => $this->record->id]))->markAsRead(),
                ])
                ->sendToDatabase(User::where('id', $this->record->user_id)->get());

            $details = [
                'name' => $this->record->user->name,
                'message' => 'Your booking has been confirmed. Thank you for choosing us!',
                'amount_paid' => $this->record->amount_paid,
                'balance' => $this->record->balance,
                'type' => 'approved_booking',
            ];

            Mail::to($this->record->user->email)->send(new MailFrontDesk($details));
        }

        // $details = [
        //     'name' => $this->record->user->name,
        //     'message' => 'Your booking has been confirmed. Thank you for choosing us!',
        //     'amount_paid' => $this->record->amount_paid,
        //     'balance' => $this->record->balance,
        //     'type' => 'approved_booking',
        // ];

        // Mail::to($this->record->user->email)->send(new MailFrontDesk($details));

        if ($this->record->id) {
            redirect(BookingResource::getUrl('view', ['record' => $this->record->id]));
        } else {
            redirect(BookingResource::getUrl('index'));
        }
    }

    public function return()
    {
        $data = $this->cancelForm->getState();

        $this->record->status = 'returned';
        $this->record->return_notes = $data['cancel_reason'];
        $this->record->save();

        $related = $this->record->relatedBookings;
        foreach ($related as $booking) {
            $booking->update([
                'status' => 'returned',
                'return_notes' => $data['cancel_reason'],
            ]);
        }

        Notification::make()
            ->success()
            ->title('Booking Returned')
            ->icon('heroicon-o-check-circle')
            ->body($this->record->user->name.' your booking has been returned')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn () => MyBookingResource::getUrl('payment', ['record' => $this->record->id]))->markAsRead(),
                // ->openUrlInNewTab()
            ])
            ->sendToDatabase(User::where('id', $this->record->user_id)->get());

        $details = [
            'name' => $this->record->user->name,
            'message' => $data['cancel_reason'],
            'type' => 'cancel_booking',
        ];

        // Mail::to($this->record->user->email)->send(new MailFrontDesk($details));

        Notification::make()
            ->success()
            ->title('Booking Returned')
            ->icon('heroicon-o-check-circle')
            ->send();

        if ($this->record->id) {
            redirect(BookingResource::getUrl('view', ['record' => $this->record->id]));
        } else {
            redirect(BookingResource::getUrl('index'));
        }
    }

    public function cancel()
    {
        $data = $this->cancelForm->getState();

        $this->record->status = 'cancelled';
        $this->record->cancel_reason = $data['cancel_reason'];
        $this->record->want_cancel = true;
        $this->record->save();

        $related = $this->record->relatedBookings;
        foreach ($related as $booking) {
            $booking->update([
                'status' => 'cancelled',
                'cancel_reason' => $data['cancel_reason'],
                'want_cancel' => true,
            ]);
        }

        Notification::make()
            ->success()
            ->title('Booking Cancelled')
            ->icon('heroicon-o-check-circle')
            ->body($this->record->user->name.' your booking has been cancelled')
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->url(fn () => MyBookingResource::getUrl('payment', ['record' => $this->record->id]))->markAsRead(),
                // ->openUrlInNewTab()
            ])
            ->sendToDatabase(User::where('id', $this->record->user_id)->get());

        $details = [
            'name' => $this->record->user->name,
            'message' => $data['cancel_reason'],
            'type' => 'cancel_booking',
        ];

        // Mail::to($this->record->user->email)->send(new MailFrontDesk($details));

        Notification::make()
            ->success()
            ->title('Booking Cancelled')
            ->icon('heroicon-o-check-circle')
            ->send();
    }
}
