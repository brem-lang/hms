<?php

namespace App\Filament\Resources\MyOrderResource\Pages;

use App\Filament\Resources\MyOrderResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListMyOrders extends ListRecords
{
    protected static string $resource = MyOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Action::make('New Order')
                ->url('/app/food-order'),
        ];
    }
}
