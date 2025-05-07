<?php

namespace App\Filament\Resources\FoodOrderResource\Pages;

use App\Filament\Resources\FoodOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFoodOrder extends CreateRecord
{
    protected static string $resource = FoodOrderResource::class;
}
