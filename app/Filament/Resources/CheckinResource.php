<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CheckinResource\Pages;
use App\Models\Booking;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
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
                    ->label('View')
                    ->color('primary')
                    ->url(fn ($record) => $record->is_occupied
                        ? CheckinResource::getUrl('checkout', ['record' => $record->id])
                        : CheckinResource::getUrl('view', ['record' => $record->id])),
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

    public static function extendChecker($roomId, $currentCheckoutDate, $newCheckoutDate, $currentBookingId)
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
            'view' => Pages\ViewCheckin::route('/{record}'),
            'checkout' => Pages\ViewCheckout::route('/{record}/checkout'),
            // 'create' => Pages\CreateCheckin::route('/create'),
            // 'edit' => Pages\EditCheckin::route('/{record}/edit'),
        ];
    }
}
