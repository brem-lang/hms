<?php

namespace App\Filament\Resources\MyBookingResource\Pages;

use App\Filament\Resources\MyBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMyBooking extends CreateRecord
{
    protected static string $resource = MyBookingResource::class;
}
