<?php

namespace App\Filament\Resources\MyBookingResource\Pages;

use App\Filament\Resources\MyBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMyBooking extends EditRecord
{
    protected static string $resource = MyBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
