<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodOrderResource\Pages;
use App\Models\FoodOrder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FoodOrderResource extends Resource
{
    protected static ?string $model = FoodOrder::class;

    protected static ?string $modelLabel = 'Foods';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Settlement';

    // public static function canViewAny(): bool
    // {
    //     return auth()->user()->isAdmin() || auth()->user()->isStaff() || auth()->user()->isFrontDesk();
    // }

    public static function canViewAny(): bool
    {
        return false;
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
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('success')
                    ->url(fn ($record) => FoodOrderResource::getUrl('view', ['record' => $record->id])),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->modifyQueryUsing(function ($query) {
                return $query->latest();
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
            'index' => Pages\ListFoodOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}/view'),
            // 'create' => Pages\CreateFoodOrder::route('/create'),
            'edit' => Pages\EditFoodOrder::route('/{record}/edit'),
        ];
    }
}
