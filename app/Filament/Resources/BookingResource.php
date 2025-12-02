<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Mail\MailFrontDesk;
use App\Models\Booking;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Settlement';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isFrontDesk();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ToggleButtons::make('is_occupied')
                    ->columnSpanFull()
                    ->label('Occupied')
                    ->boolean()
                    ->inline(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->columns(1);
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
                TextColumn::make('status')
                    ->toggleable()
                    ->badge()->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'warning',
                        'cancelled' => 'danger',
                        'done' => 'success',
                        'returned' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'For CheckIn',
                        'pending' => 'Confirmation Payment',
                        default => __(ucfirst($state)),
                    })
                    ->searchable(),
                // TextColumn::make('suiteRoom.is_occupied')
                //     ->label('Occupied')
                //     ->toggleable()
                //     ->badge()->color(fn (string $state): string => match ($state) {
                //         '1' => 'success',
                //         '0' => 'danger',
                //     })
                //     ->formatStateUsing(fn (string $state): string => $state ? 'Yes' : 'No'),
                TextColumn::make('suiteRoom.name')
                    ->label('Room Number')
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->searchable(),
                // TextColumn::make('type')
                //     ->label('Booking Type')
                //     ->toggleable()
                //     ->formatStateUsing(function ($state) {
                //         return $state === 'online' ? 'Online' : 'Walk-in';
                //     })
                //     ->searchable(),
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
                SelectFilter::make('room_id')
                    ->label('Room Type')
                    ->relationship('room', 'name'),
            ])
            ->poll('3s')
            ->actions([
                // Tables\Actions\EditAction::make()
                //     ->modalWidth('md'),
                ActionsAction::make('email_front_desk')
                    ->label('Mail')
                    ->icon('heroicon-o-envelope')
                    ->action(function ($data, $record) {
                        $details = [
                            'name' => $record->user->name,
                            'message' => $data['message'],
                            'type' => 'mail_front_desk',
                        ];

                        Mail::to($record->user->email)->send(new MailFrontDesk($details));

                        Notification::make()
                            ->title('Email Sent')
                            ->success()
                            ->send();
                    })
                    ->form([
                        Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->maxLength(500)
                            ->placeholder('Type your message here'),
                    ])
                    ->visible(function ($record) {
                        return auth()->user()->isFrontDesk();
                    }),
                ActionsAction::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->label('Cancel')
                    ->requiresConfirmation()
                    ->action(function (Booking $booking) {
                        $booking->status = 'cancelled';
                        $booking->save();
                    })
                    ->visible(fn (Booking $booking) => $booking->status == 'completed' && $booking->type == 'walkin_booking'),
                ActionsAction::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->url(fn ($record) => BookingResource::getUrl('view', ['record' => $record->id])),
                ActionsAction::make('edit_date')
                    ->label('Edit Date')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->action(function ($record, $data) {
                        $record->check_in_date = $data['check_in_date'];
                        $record->check_out_date = $data['check_out_date'];
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('Date Updated')
                            ->send();
                    })
                    ->form(function (Booking $booking) {
                        return [
                            DateTimePicker::make('check_in_date')
                                ->label('Check In Date')
                                ->date('F d, Y h:i A')
                                ->required()
                                ->formatStateUsing(function ($record) {
                                    return $record->check_in_date;
                                }),
                            DateTimePicker::make('check_out_date')
                                ->label('Check Out Date')
                                ->date('F d, Y h:i A')
                                ->required()
                                ->formatStateUsing(function ($record) {
                                    return $record->check_out_date;
                                }),
                        ];
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->where('type', '!=', 'bulk_online')->latest());
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
