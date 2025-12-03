<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CheckinResource\Pages;
use App\Models\Booking;
use App\Models\Charge;
use App\Models\Food;
use Carbon\Carbon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CheckinResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Checkin & Checkout';

    protected static ?string $navigationGroup = 'Settlement';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isFrontDesk();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {

        $data = parent::getEloquentQuery()->where('type', '!=', 'bulk_head_online')
            ->where('status', 'completed');

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('user.name')
                    ->label('Guest Name')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('room.name')
                    ->label('Room Type')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('check_in_date')
                    ->label('Check In Date')
                    ->date('F d, Y h:i A')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('check_out_date')
                    ->label('Check Out Date')
                    ->date('F d, Y h:i A')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('is_occupied')
                    ->label('Occupied')
                    ->toggleable()
                    ->badge()->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => $state ? 'Yes' : 'No'),
                TextColumn::make('suiteRoom.name')
                    ->label('Room Number')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Booking Type')
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        return $state === 'online' ? 'Online' : ($state === 'bulk_online' ? 'Online' : 'Walk-in');
                    })
                    ->searchable(),
                TextColumn::make('is_no_show')
                    ->label('Guest Status')
                    ->toggleable()
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1' => 'No Show', // ⬅️ If TRUE (1), display the label "No Show"
                        '0' => '',        // ⬅️ If FALSE (0), display EMPTY STRING
                        default => '',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        // Apply the color only when the badge is visible (i.e., when $state is '1')
                        '1' => 'danger', // ⬅️ Use danger/red color when the status is true
                        '0' => 'gray',   // If false, the color doesn't matter much as the badge is empty
                        default => 'gray',
                    }),
            ])
            ->filters([
                Filter::make('name')
                    ->form([
                        TextInput::make('name')
                            ->label('Name')
                            ->maxLength(255),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['name'],
                                fn (Builder $query, $name): Builder => $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%'.$name.'%'))
                                    ->orWhereHas(
                                        'walkingGuest',
                                        fn ($q) => $q->where('first_name', 'like', '%'.$name.'%')
                                            ->orWhere('last_name', 'like', '%'.$name.'%')
                                    )
                            );
                    }),
                SelectFilter::make('type')
                    ->label('Booking Type')
                    ->options([
                        'online' => 'Online',
                        'walkin_booking' => 'Walk-in',
                    ]),
                SelectFilter::make('room_id')
                    ->label('Room Type')
                    ->relationship('room', 'name'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([
                Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn ($record) => BookingResource::getUrl('view', ['record' => $record->id]))
                    ->openUrlInNewTab(),
                Action::make('check_in')
                    ->icon('heroicon-o-check-circle')
                    ->label('Check In')
                    ->color('success')
                    ->visible(fn ($record) => $record->is_occupied == 0)
                    ->modalWidth('5xl')
                    // ->disabled(function ($record) {
                    //     if (Carbon::parse($record->check_in_date, 'Asia/Manila')->setTimezone('UTC')->format('Y-m-d H:i:s') > Carbon::now('UTC')->format('Y-m-d H:i:s')) {
                    //         return true;
                    //     }
                    // })
                    ->form([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(fn ($record) => $record->user->name),
                                TextInput::make('no_persons')
                                    ->label('Number of Persons')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(function ($record) {
                                        $booking = $record;

                                        return $booking->type != 'bulk_head_online' ? $booking->no_persons : $booking->relatedBookings->sum('no_persons');
                                    }),
                                TextInput::make('additional_persons')
                                    ->label('Additional Persons')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(function ($record) {
                                        $booking = $record;

                                        return $booking->type != 'bulk_head_online' ? $booking->additional_persons : $booking->relatedBookings->sum('additional_persons');
                                    }),
                                TextInput::make('contact_number')
                                    ->label('Contact Number')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(fn ($record) => $record->user->contact_number),
                                TextInput::make('check_in_time')
                                    ->label('Check In Time')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(fn ($record) => Carbon::parse($record->check_in_date)->format('F j, Y h:i A')),
                                TextInput::make('status')
                                    ->label('Status')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(function ($record) {
                                        $booking = $record;

                                        return $booking->status == 'completed' ? 'For CheckIn' : ($booking->status == 'done' ? 'Settled' : ucfirst($booking->status));
                                    }),
                                TextInput::make('check_out_time')
                                    ->label('Check Out Time')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(fn ($record) => Carbon::parse($record->check_out_date)->format('F j, Y h:i A')),
                                TextInput::make('start_date')
                                    ->label('Booking Start')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->visible(function ($record) {
                                        return $record->room_id != 4;
                                    })
                                    ->formatStateUsing(fn ($record) => Carbon::parse($record->start_date)->format('F j, Y h:i A')),
                                TextInput::make('amount')
                                    ->label('Room Booking Fee')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->prefix('₱ ')
                                    ->formatStateUsing(function ($record) {
                                        $booking = $record;

                                        return $booking->type != 'bulk_head_online' ? number_format($booking->amount_to_pay, 2) : number_format($booking->relatedBookings->sum('amount_to_pay'), 2);
                                    }),
                                TextInput::make('end_date')
                                    ->label('Booking End')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->visible(function ($record) {
                                        return $record->room_id != 4;
                                    })
                                    ->formatStateUsing(fn ($record) => Carbon::parse($record->end_date)->format('F j, Y h:i A')),
                                TextInput::make('amount_paid')
                                    ->label('Amount Paid')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->prefix('₱ ')
                                    ->formatStateUsing(function ($record) {
                                        $booking = $record;

                                        return $booking->type != 'bulk_head_online' ? number_format($booking->amount_paid, 2) : number_format($booking->relatedBookings->sum('amount_paid'), 2);
                                    }),
                                TextInput::make('date_of_booking')
                                    ->label('Date of Booking')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->visible(function ($record) {
                                        return $record->room_id != 4;
                                    })
                                    ->formatStateUsing(fn ($record) => Carbon::parse($record->created_at)->format('F j, Y h:i A')),
                                TextInput::make('balance')
                                    ->label('Balance Due')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->prefix('₱ ')
                                    ->formatStateUsing(function ($record) {
                                        $booking = $record;

                                        $chargesAmount = 0;
                                        if ($booking->type != 'bulk_head_online') {
                                            foreach ($booking->additional_charges ?? [] as $charge) {
                                                $chargesAmount += $charge['total_charges'];
                                            }
                                        } else {
                                            foreach ($booking->relatedBookings as $value) {
                                                foreach ($value->additional_charges ?? [] as $charge) {
                                                    $chargesAmount += $charge['total_charges'];
                                                }
                                            }
                                        }

                                        return number_format($booking->balance + $chargesAmount, 2);
                                    }),
                                TextInput::make('days')
                                    ->label('Total days')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(fn ($record) => $record->days),
                                TextInput::make('suite_type')
                                    ->label('Suite Type')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(fn ($record) => $record->room->name),
                                TextInput::make('duration')
                                    ->label('Duration Hrs')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(fn ($record) => $record->duration),
                                TextInput::make('room')
                                    ->label('Room Number')
                                    ->dehydrated(false)
                                    ->readOnly()
                                    ->formatStateUsing(function ($record) {
                                        $booking = $record;

                                        if ($booking->type !== 'bulk_head_online') {
                                            // Logic for a single room booking (similar to your @if block)
                                            // Uses optional chaining on suiteRoom relationship and coalescing to '-'
                                            $roomName = $booking->suiteRoom->name ?? '-';

                                            return ucfirst($roomName);
                                        } else {
                                            // Logic for bulk/related bookings (similar to your @foreach block)

                                            // Collect the names of all related rooms
                                            $roomNames = $booking->relatedBookings
                                                ->map(function ($value) {
                                                    // Get the name, or '-' if not available, and capitalize it
                                                    $name = $value->suiteRoom->name ?? '-';

                                                    return ucfirst($name);
                                                })
                                                ->filter() // Remove any empty/null names just in case
                                                ->implode(', '); // Join them all together with a comma and space

                                            return $roomNames;
                                        }
                                    }),

                            ])
                            ->columns(2),
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
                    ->modalSubmitActionLabel('Check In')
                    ->action(function ($record, $data) {

                        $newCharges = CheckinResource::cleanAdditionalCharges($data['room_charges']);
                        $existingCharges = (array) ($record->additional_charges ?? []);
                        $record->food_charges = CheckinResource::cleanAdditionalCharges($data['food_charges']);
                        $record->additional_charges = array_merge($existingCharges, $newCharges);
                        $record->is_occupied = 1;
                        $record->save();

                        $room = $record->suiteRoom;

                        // Check if any OTHER active booking is already occupying this room
                        $active = $room->bookings()
                            ->where('id', '!=', $record->id)
                            ->where('is_occupied', 1)
                            ->exists();

                        // Only mark room as occupied if no other booking has occupied it
                        if (! $active) {
                            $room->is_occupied = 1;
                            $room->save();
                        }

                        Notification::make()
                            ->success()
                            ->title('Check In')
                            ->send();
                    }),
                Action::make('check_out')
                    ->icon('heroicon-o-check-circle')
                    ->label('Check Out')
                    ->color('warning')
                    ->modalWidth('5xl')
                    ->modalSubmitActionLabel('Check Out')
                    ->visible(fn ($record) => $record->is_occupied == 1)
                    ->form(function ($record) {
                        return [
                            TextInput::make('amount')
                                ->label('Amount')
                                ->disabled()
                                ->prefix('₱ ')
                                ->formatStateUsing(function ($record) {
                                    return number_format($record->amount_to_pay, 2);
                                }),
                            TextInput::make('amount_paid')
                                ->label('Amount Paid')
                                ->disabled()
                                ->prefix('₱ ')
                                ->formatStateUsing(function ($record) {
                                    // return $record->amount_paid;
                                    return number_format($record->amount_paid, 2);
                                }),
                            TextInput::make('balance')
                                ->label('Balance')
                                ->disabled()
                                ->prefix('₱ ')
                                ->formatStateUsing(function ($record) {
                                    $chargesAmount = 0;
                                    foreach ($record['additional_charges'] ?? [] as $charge) {
                                        $chargesAmount += $charge['total_charges'];
                                    }

                                    $foodChargesAmount = 0;
                                    foreach ($record['food_charges'] ?? [] as $charge) {
                                        $foodChargesAmount += $charge['total_charges'];
                                    }

                                    return number_format($record->balance + $chargesAmount + $foodChargesAmount, 2);
                                }),

                            Repeater::make('charges')
                                ->formatStateUsing(fn ($record) => $record->additional_charges)->label('Room Charges')
                                ->reorderable(false)
                                ->schema([
                                    Select::make('name')
                                        ->disabled()
                                        ->options(Charge::pluck('name', 'id')),
                                    TextInput::make('amount')->numeric()->required()->disabled(),
                                    TextInput::make('quantity')->numeric()->required()->disabled(),
                                    TextInput::make('total_charges')->numeric()->required()->disabled(),
                                ])
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->columns(4),

                            Repeater::make('food_charges')
                                ->formatStateUsing(fn ($record) => $record->food_charges)->label('Food Charges')
                                ->reorderable(false)
                                ->schema([
                                    Select::make('name')
                                        ->disabled()
                                        ->options(Food::pluck('name', 'id')),
                                    TextInput::make('amount')->numeric()->required()->disabled(),
                                    TextInput::make('quantity')->numeric()->required()->disabled(),
                                    TextInput::make('total_charges')->numeric()->required()->disabled(),
                                ])
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->columns(4),
                        ];
                    })
                    ->action(function ($record, $data) {

                        $chargesAmount = 0;
                        foreach ($record['additional_charges'] ?? [] as $charge) {
                            $chargesAmount += $charge['total_charges'];
                        }

                        $foodChargesAmount = 0;
                        foreach ($record['food_charges'] ?? [] as $charge) {
                            $foodChargesAmount += $charge['total_charges'];
                        }

                        $record->status = 'done';
                        $record->is_occupied = 0;
                        $record->balance = 0;
                        $record->amount_paid = $record->amount_to_pay + $chargesAmount + $foodChargesAmount;
                        $record->amount_to_pay = $record->amount_to_pay + $chargesAmount + $foodChargesAmount;
                        $record->suiteRoom->is_occupied = 0;
                        $record->suiteRoom->save();
                        $record->save();

                        if ($record->getBookingHead) {
                            $record->getBookingHead->update([
                                'status' => 'done',
                            ]);
                        }

                        Notification::make()
                            ->success()
                            ->title('Check Out')
                            ->send();
                    }),
                Action::make('add_person')
                    ->icon('heroicon-o-user-plus')
                    ->label('Add Person')
                    ->color('warning')
                    ->visible(fn ($record) => $record->is_occupied == 1 && $record->room_id == 4)
                    ->form([
                        TextInput::make('no_persons')
                            ->numeric()
                            ->label('Number of Persons')
                            ->formatStateUsing(function ($record) {
                                return $record->additional_persons;
                            })
                            ->required()
                            ->maxLength(255),

                    ])
                    ->modalWidth('lg')
                    ->action(function ($record, $data) {

                        $packageArray = json_decode($record->selected_package, true);
                        $itemName = $packageArray['item'] ?? null;
                        $chargeID = null;

                        $chargeID = match ($itemName) {
                            'Basic Package - Option 1',
                            'Basic Package - Option 2' => 5,

                            'Standard Package - Option 1',
                            'Standard Package - Option 2' => 6,

                            'Premium Package - Option 1',
                            'Premium Package - Option 2' => 7,

                            default => 5,
                        };

                        $charge = Charge::find($chargeID);

                        $newExtendCharge = [
                            'name' => (string) $charge->id,
                            'amount' => number_format($charge->amount, 2, '.', ''),
                            'quantity' => $data['no_persons'],
                            'total_charges' => $charge->amount * $data['no_persons'],
                        ];

                        $existingCharges = $record->additional_charges ?? [];

                        if (! is_array($existingCharges)) {
                            $existingCharges = [];
                        }

                        $existingCharges[] = $newExtendCharge;

                        $record->additional_charges = $existingCharges;
                        $record->additional_persons = $data['no_persons'];
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('Person Added')
                            ->send();
                    }),
                Action::make('extend')
                    ->icon('heroicon-o-clock')
                    ->label('Extend')
                    ->color('warning')
                    ->visible(fn ($record) => $record->is_occupied == 1 && $record->is_extend == 0)
                    ->form([
                        DateTimePicker::make('check_out_date')
                            ->label('Check Out Date')
                            ->date('F d, Y h:i A')
                            ->dehydrated(false)
                            ->readOnly()
                            ->formatStateUsing(function ($record) {
                                return $record->check_out_date;
                            }),
                        DateTimePicker::make('extend_date')
                            ->label('Extend Date')
                            ->date('F d, Y h:i A')
                            ->default(now())
                            ->formatStateUsing(function ($record) {
                                return $record->extend_date;
                            }),
                    ])
                    ->modalWidth('lg')
                    ->action(function ($record, $data) {
                        if (CheckinResource::extendChecker($record->room_id, $record->check_out_date, $data['extend_date'], $record->id)) {
                            Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body('Please select different dates for extending')
                                ->send();

                            return;
                        }

                        $diffHours = (int) abs(Carbon::parse($data['extend_date'])->diffInHours(Carbon::parse($record->check_out_date)));

                        if ($record->room_id != 4) {
                            $extendCharge = Charge::find(2);

                            $newExtendCharge = [
                                'name' => (string) $extendCharge->id,
                                'amount' => number_format($extendCharge->amount, 2, '.', ''),
                                'quantity' => (string) $diffHours,
                                'total_charges' => $extendCharge->amount * $diffHours,
                            ];

                            $existingCharges = $record->additional_charges ?? [];

                            if (! is_array($existingCharges)) {
                                $existingCharges = [];
                            }

                            $existingCharges[] = $newExtendCharge;
                            $record->additional_charges = $existingCharges;
                            $record->is_extend = 1;
                            $record->extend_date = $data['extend_date'];
                            $record->save();
                        }

                        if ($record->room_id == 4) {
                            $extendCharge = Charge::find(4);

                            $newExtendCharge = [
                                'name' => (string) $extendCharge->id,
                                'amount' => number_format($extendCharge->amount, 2, '.', ''),
                                'quantity' => (string) $diffHours,
                                'total_charges' => $extendCharge->amount * $diffHours,
                            ];

                            $existingCharges = $record->additional_charges ?? [];

                            if (! is_array($existingCharges)) {
                                $existingCharges = [];
                            }

                            $existingCharges[] = $newExtendCharge;

                            $record->additional_charges = $existingCharges;
                            $record->is_extend = 1;
                            $record->extend_date = $data['extend_date'];
                            $record->save();
                        }

                        Notification::make()
                            ->success()
                            ->title('Booking Extended')
                            ->send();
                    }),
            ]);
    }

    public static function cleanAdditionalCharges(array $charges): array
    {
        if (empty($charges) || ! is_array($charges)) {
            return [];
        }

        $cleanedCharges = [];

        foreach ($charges as $charge) {
            // Check if the required keys (name, amount) have non-null/non-empty values.
            if (
                isset($charge['name']) && ! empty($charge['name']) &&
                isset($charge['amount']) && ! empty($charge['amount'])
            ) {
                // Only include entries that look valid.
                $cleanedCharges[] = $charge;
            }
        }

        return $cleanedCharges;
    }

    public static function ExtendChecker($roomId, $currentCheckoutDate, $newCheckoutDate, $currentBookingId)
    {
        $extensionStart = $currentCheckoutDate;
        $extensionEnd = $newCheckoutDate;

        $conflicts = Booking::query()
            ->where('suite_room_id', $roomId)
            ->where('id', '!=', $currentBookingId)
            ->where('status', 'completed')
            ->where(function (Builder $query) use ($extensionStart, $extensionEnd) {

                $query->whereBetween('check_in_date', [$extensionStart, $extensionEnd]);

                $query->orWhereBetween('check_out_date', [$extensionStart, $extensionEnd]);

                $query->orWhere(function (Builder $q) use ($extensionStart, $extensionEnd) {
                    $q->where('check_in_date', '<', $extensionStart)
                        ->where('check_out_date', '>', $extensionEnd);
                });
            })
            ->exists();

        return $conflicts;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCheckins::route('/'),
            // 'create' => Pages\CreateCheckin::route('/create'),
            // 'edit' => Pages\EditCheckin::route('/{record}/edit'),
        ];
    }
}
