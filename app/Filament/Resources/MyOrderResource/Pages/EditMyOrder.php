<?php

namespace App\Filament\Resources\MyOrderResource\Pages;

use App\Filament\Resources\MyOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyOrder extends EditRecord
{
    protected static string $resource = MyOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
