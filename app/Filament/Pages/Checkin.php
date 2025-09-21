<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BookingResource;
use App\Models\Booking;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class Checkin extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.checkin';

    protected static ?string $title = 'Checkin & Checkout';

    protected static ?string $navigationGroup = 'Settlement';

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isFrontDesk();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Booking::query()->where('status', 'completed')->latest())
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
                // TextColumn::make('status')
                //     ->toggleable()
                //     ->badge()->color(fn (string $state): string => match ($state) {
                //         'pending' => 'gray',
                //         'completed' => 'success',
                //         'cancelled' => 'danger',
                //     })
                //     ->formatStateUsing(fn (string $state): string => __(ucfirst($state)))
                //     ->searchable(),
                TextColumn::make('suiteRoom.is_occupied')
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
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->suiteRoom->is_occupied == 0)
                    ->action(function ($record) {

                        $record->suiteRoom->is_occupied = 1;
                        $record->suiteRoom->save();

                        Notification::make()
                            ->success()
                            ->title('Check In')
                            ->send();
                    }),
                Action::make('check_out')
                    ->icon('heroicon-o-check-circle')
                    ->label('Check Out')
                    ->color('warning')
                    // ->requiresConfirmation()
                    ->visible(fn ($record) => $record->suiteRoom->is_occupied == 1)
                    ->form(function ($record) {
                        return [
                            TextInput::make('amount')
                                ->label('Amount')
                                ->disabled()
                                ->formatStateUsing(function ($record) {
                                    return $record->amount_to_pay;
                                }),
                            TextInput::make('amount_paid')
                                ->label('Amount Paid')
                                ->disabled()
                                ->formatStateUsing(function ($record) {
                                    return $record->amount_paid;
                                }),
                            TextInput::make('balance')
                                ->label('Balance')
                                ->disabled()
                                ->formatStateUsing(function ($record) {
                                    $chargesAmount = 0;
                                    foreach ($record['additional_charges'] ?? [] as $charge) {
                                        $chargesAmount += $charge['amount'];
                                    }

                                    return $record->balance + $chargesAmount;
                                }),

                            Repeater::make('charges')
                                ->formatStateUsing(fn ($record) => $record->additional_charges)
                                ->label('Additional Charges')
                                ->reorderable(false)
                                ->schema([
                                    TextInput::make('name')->required(),
                                    TextInput::make('amount')->numeric()->required(),
                                ])
                                ->addable(false)
                                ->deletable(false)
                                ->reorderable(false)
                                ->columns(2),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'paid' => 'Paid',
                                    'pending' => 'Pending',
                                ]),
                        ];
                    })
                    ->action(function ($record, $data) {
                        if ($data['status'] == 'paid') {
                            $record->status = 'done';
                            $record->balance = 0;
                            $record->amount_paid = $record->amount_to_pay;
                            $record->suiteRoom->is_occupied = 0;
                            $record->suiteRoom->save();
                            $record->save();

                            Notification::make()
                                ->success()
                                ->title('Check Out')
                                ->send();
                        }
                        // $record->status = 'done';
                        // $record->suiteRoom->is_occupied = 0;
                        // $record->suiteRoom->save();
                        // $record->save();

                        // Notification::make()
                        //     ->success()
                        //     ->title('Check In')
                        //     ->send();
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }
}
