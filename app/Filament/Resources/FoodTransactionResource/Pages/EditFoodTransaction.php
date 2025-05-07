<?php

namespace App\Filament\Resources\FoodTransactionResource\Pages;

use App\Filament\Resources\FoodTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFoodTransaction extends EditRecord
{
    protected static string $resource = FoodTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
