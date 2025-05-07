<?php

namespace App\Filament\Resources\FoodTransactionResource\Pages;

use App\Filament\Resources\FoodTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFoodTransaction extends CreateRecord
{
    protected static string $resource = FoodTransactionResource::class;
}
