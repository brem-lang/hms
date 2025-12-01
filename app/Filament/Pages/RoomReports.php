<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\MyBookingResource;
use App\Models\Booking;
use App\Models\SuiteRoom;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RoomReports extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.room-reports';

    protected static ?string $navigationGroup = 'Accommodation Control Panel';

    protected static ?int $navigationSort = 4;

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isFrontDesk();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SuiteRoom::query())
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('room.name')
                    ->label('Room Type')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Room Number')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive'),
                TextColumn::make('is_occupied')
                    ->label('Occupied')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No'),
            ])
            ->filters([
                SelectFilter::make('room_id')
                    ->label('Room Type')
                    ->relationship('room', 'name')
                    ->placeholder('All'),
                SelectFilter::make('is_active')
                    ->label('Status')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->placeholder('All'),
                SelectFilter::make('is_occupied')
                    ->label('Occupied')
                    ->options([
                        '1' => 'Yes',
                        '0' => 'No',
                    ])
                    ->placeholder('All'),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->visible(function ($record) {
                        $booking = Booking::where('room_id', $record->room_id)->whereHas('suiteRoom', function ($query) use ($record) {
                            $query->where('is_occupied', 1)->where('id', $record->id);
                        })->first();

                        if ($booking) {
                            return true;
                        }
                    })
                    ->url(function ($record) {

                        $booking = Booking::where('room_id', $record->room_id)->whereHas('suiteRoom', function ($query) use ($record) {
                            $query->where('is_occupied', 1)->where('id', $record->id);
                        })->first();

                        // dd($booking);
                        return BookingResource::getUrl('view', ['record' => $booking->id]);
                    })
                // ->url(fn ($record) => MyBookingResource::getUrl('payment', ['record' => $record->id]))
                ,
            ])
            ->bulkActions([
                //
            ]);
    }
}
