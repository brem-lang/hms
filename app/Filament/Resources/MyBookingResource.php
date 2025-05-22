<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyBookingResource\Pages;
use App\Models\Booking;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MyBookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $modelLabel = 'My Booking';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Room Management';

    public static function canViewAny(): bool
    {
        return auth()->user()->isCustomer();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->columnSpanFull()
                    ->required()
                    ->minDate(now()->startOfDay())
                    ->reactive(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50])
            ->columns([
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
                TextColumn::make('duration')
                    ->label('Duration')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('suiteRoom.name')
                    ->label('Room Number')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable(),
                TextColumn::make('status')
                    ->toggleable()
                    ->badge()->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => __(ucfirst($state)))
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(function ($record) {
                        return $record->status !== 'pending';
                    }),
                // DeleteAction::make()
                //     ->hidden(function ($record) {
                //         return $record->status !== 'pending';
                //     }),
                Action::make('cancel_booking')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->label('Cancel')
                    ->action(function ($record) {
                        $record->status = 'cancelled';
                        $record->save();
                    })
                    ->color('danger')
                    // ->hidden(function ($record) {
                    //     return $record->status !== 'pending';
                    // })
                    ->visible(function ($record) {
                        return $record->status === 'completed';
                        // return \Carbon\Carbon::parse($record->start_date)->greaterThan(now()->addDays(1));
                    }),
                Action::make('payment')
                    ->label('Pay')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->url(fn ($record) => MyBookingResource::getUrl('payment', ['record' => $record->id]))
                    // ->openUrlInNewTab()
                    ->hidden(function ($record) {
                        return $record->status !== 'pending';
                    }),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->where('user_id', auth()->user()->id)->latest();
            });
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
            'index' => Pages\ListMyBookings::route('/'),
            'payment' => Pages\PayMyBoooking::route('/{record}/payment'),
            // 'create' => Pages\CreateMyBooking::route('/create'),
            // 'edit' => Pages\EditMyBooking::route('/{record}/edit'),
        ];
    }
}
