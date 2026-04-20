<?php

namespace App\Filament\Resources\CheckinResource\Pages;

use App\Filament\Resources\CheckinResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCheckins extends ListRecords
{
    protected static string $resource = CheckinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'checkIn' => Tab::make('Check-in')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_occupied', 0)),
            'checkOut' => Tab::make('Check-out')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_occupied', 1)),
        ];
    }
}
