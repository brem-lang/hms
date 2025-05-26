<?php

namespace App\Livewire;

use App\Models\SuiteRoom;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RoomsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $unavailable = SuiteRoom::query()
            ->where('is_active', false)
            ->where('is_occupied', false)
            ->count();
        $occupied = SuiteRoom::query()
            ->where('is_occupied', true)
            ->count();
        $available = SuiteRoom::query()
            ->where('is_active', true)
            ->where('is_occupied', false)
            ->count();

        return [
            Stat::make('Occupied rooms', $occupied),
            Stat::make('Available rooms', $available),
            Stat::make('Unavailable rooms', $unavailable),
        ];
    }
}
