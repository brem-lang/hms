<?php

namespace App\Filament\Pages;

use App\Models\SuiteRoom;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
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
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
