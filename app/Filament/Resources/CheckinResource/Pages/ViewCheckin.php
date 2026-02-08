<?php

namespace App\Filament\Resources\CheckinResource\Pages;

use App\Filament\Resources\CheckinResource;
use App\Mail\MailFrontDesk;
use App\Models\Booking;
use App\Models\Charge;
use App\Models\Food;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Mail;

class ViewCheckin extends Page
{
    public $record;

    public ?array $formData = [];

    protected static string $resource = CheckinResource::class;

    protected static string $view = 'filament.resources.checkin-resource.pages.view-checkin';

    public function getTitle(): string
    {
        return 'Check-In Details - '.$this->record->booking_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('transfer')
                ->label('Transfer')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->disabled($this->record->is_occupied)
                ->form(
                    [
                        Select::make('room')
                            ->label('Room')
                            ->options($this->record->room
                                ->suite_rooms()
                                ->where('id', '!=', $this->record->suite_room_id)
                                ->where('is_occupied', 0)
                                ->pluck('name', 'id')),
                    ]
                )
                ->action(function ($data, $record) {
                    $this->record->update([
                        'suite_room_id' => (int) $data['room'],
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Transfer')
                        ->body('Transfer has been successful.')
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }

    public function mount(Booking $record): void
    {
        $this->record = $record;
        $this->form->fill([
            'room_charges' => $record->additional_charges ?? [],
            'food_charges' => $record->food_charges ?? [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('room_charges')
                    ->label('Room Charges')
                    ->reorderable(false)
                    ->schema([
                        Select::make('name')
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
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

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->live()
                            ->numeric()
                            ->minValue(1)
                            ->afterStateUpdated(function (Set $set, ?string $state, Get $get) {
                                $quantity = (float) $state;
                                $amount = (float) $get('amount') ?: 0;
                                $set('total_charges', $quantity * $amount);
                            }),

                        TextInput::make('total_charges')
                            ->label('Total Charges')
                            ->prefix('PHP')
                            ->numeric()
                            ->readOnly()
                            ->dehydrateStateUsing(function (Get $get) {
                                $quantity = (float) $get('quantity') ?: 0;
                                $amount = (float) $get('amount') ?: 0;

                                return $quantity * $amount;
                            }),
                    ])
                    ->columns(4),

                Repeater::make('food_charges')
                    ->label('Food Charges')
                    ->reorderable(false)
                    ->schema([
                        Select::make('name')
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->label('Charge Name')
                            ->options(Food::pluck('name', 'id'))
                            ->live()
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $charge = Food::find($state);
                                $set('amount', $charge?->price);
                            })
                            ->searchable(),

                        TextInput::make('amount')
                            ->label('Amount')
                            ->prefix('PHP')
                            ->numeric()
                            ->readOnly(),

                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->live()
                            ->numeric()
                            ->minValue(1)
                            ->afterStateUpdated(function (Set $set, ?string $state, Get $get) {
                                $quantity = (float) $state;
                                $amount = (float) $get('amount') ?: 0;
                                $set('total_charges', $quantity * $amount);
                            }),

                        TextInput::make('total_charges')
                            ->label('Total Charges')
                            ->prefix('PHP')
                            ->numeric()
                            ->readOnly()
                            ->dehydrateStateUsing(function (Get $get) {
                                $quantity = (float) $get('quantity') ?: 0;
                                $amount = (float) $get('amount') ?: 0;

                                return $quantity * $amount;
                            }),
                    ])
                    ->columns(4),
            ])
            ->statePath('formData');
    }

    public function checkIn()
    {
        $checkIn = Carbon::parse($this->record->check_in_date)->timezone('Asia/Manila');

        // Compare to today's date in Manila
        if (! $checkIn->isToday('Asia/Manila')) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Check-in date is not today')
                ->send();

            return;
        }

        $data = $this->form->getState();

        $newCharges = CheckinResource::cleanAdditionalCharges($data['room_charges']);
        $existingCharges = (array) ($this->record->additional_charges ?? []);
        $this->record->food_charges = CheckinResource::cleanAdditionalCharges($data['food_charges']);
        $this->record->additional_charges = array_merge($existingCharges, $newCharges);
        $this->record->is_occupied = 1;
        $this->record->save();

        $room = $this->record->suiteRoom;

        // Check if any OTHER active booking is already occupying this room
        $active = $room->bookings()
            ->where('id', '!=', $this->record->id)
            ->where('is_occupied', 1)
            ->exists();

        if ($active) {

            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Room is already occupied by another booking. Transfer the booking to another room.')
                ->send();

            return;
        }

        // Only mark room as occupied if no other booking has occupied it
        if (! $active) {
            $room->is_occupied = 1;
            $room->save();
        }

        if ($this->record->room_id != 4) {
            $details = [
                'name' => $this->record->user->name,
                'message' => 'You have been checked in successfully. Thank you for choosing us!',
                'amount_paid' => $this->record->amount_paid ?? 0,
                'balance' => $this->record->balance ?? 0,
                'type' => 'check_in',
            ];

            Mail::to($this->record->type == 'online' ? $this->record->user->email : $this->record->walkingGuest->email)->send(new MailFrontDesk($details));
        } else {
            $details = [
                'name' => $this->record->organization.' '.$this->record->position,
                'message' => 'You have been checked in successfully. Thank you for choosing us!',
                'amount_paid' => $this->record->amount_paid ?? 0,
                'balance' => $this->record->balance ?? 0,
                'type' => 'check_in',
            ];
            Mail::to($this->record->email)->send(new MailFrontDesk($details));
        }

        Notification::make()
            ->success()
            ->title('Check In')
            ->send();

        return redirect(CheckinResource::getUrl('index'));
    }
}
