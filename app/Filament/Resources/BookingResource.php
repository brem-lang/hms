<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function canViewAny(): bool
    {
        return ! auth()->user()->isCustomer();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('start_date')
                    ->required()
                    ->minDate(now()->startOfDay())
                    ->live()
                    ->reactive(),
                DatePicker::make('end_date')
                    ->live()
                    ->required()
                    ->minDate(now()->startOfDay()),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('user.name')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('room.name')
                    ->label('Room Type')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('F d, Y h:i A')->timezone('Asia/Manila')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('F d, Y h:i A')->timezone('Asia/Manila')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('status')
                    ->toggleable()
                    ->badge()->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => __(ucfirst($state)))
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Booking Type')
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        return $state === 'online_booking' ? 'Online' : 'Walk-in';
                    })
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Guest')
                    ->options(User::where('role', 'customer')->get()->pluck('name', 'id')),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ActionsAction::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->url(fn ($record) => BookingResource::getUrl('view', ['record' => $record->id])),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->latest());
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
            'index' => Pages\ListBookings::route('/'),
            'view' => Pages\ViewBookings::route('/{record}'),
            // 'create' => Pages\CreateBooking::route('/create'),
            // 'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
