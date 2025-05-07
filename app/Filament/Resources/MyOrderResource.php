<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MyOrderResource\Pages;
use App\Models\FoodOrder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MyOrderResource extends Resource
{
    protected static ?string $model = FoodOrder::class;

    protected static ?string $modelLabel = 'My Orders';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Food Management';

    public static function canViewAny(): bool
    {
        return auth()->user()->isCustomer();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('food.name')
                    ->label('Food Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Quantity')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('notes')
                    ->label('Notes')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount_to_pay')
                    ->label('Amount')
                    ->sortable()
                    ->searchable()
                    ->money('php', true),
                TextColumn::make('status')
                    ->label('Status')
                    ->toggleable()
                    ->badge()->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => __(ucfirst($state))),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('payment')
                    ->label('Pay')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->url(fn ($record) => MyOrderResource::getUrl('payment', ['record' => $record->id]))
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
            'index' => Pages\ListMyOrders::route('/'),
            'payment' => Pages\ViewMyOrder::route('/{record}/payment'),
            // 'create' => Pages\CreateMyOrder::route('/create'),
            // 'edit' => Pages\EditMyOrder::route('/{record}/edit'),
        ];
    }
}
