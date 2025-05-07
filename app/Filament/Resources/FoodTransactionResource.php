<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodTransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FoodTransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $modelLabel = 'Food Transactions';

    protected static ?string $navigationGroup = 'Transaction';

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
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
                TextColumn::make('foodOrder.food.name')
                    ->label('Food Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('foodOrder.amount_to_pay')
                    ->label('Amount')
                    ->sortable()
                    ->searchable()
                    ->money('php', true),
                TextColumn::make('foodOrder.status')
                    ->label('Status')
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
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('type', 'foods')->latest();
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
            'index' => Pages\ListFoodTransactions::route('/'),
            // 'create' => Pages\CreateFoodTransaction::route('/create'),
            // 'edit' => Pages\EditFoodTransaction::route('/{record}/edit'),
        ];
    }
}
