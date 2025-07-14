<?php

namespace App\Filament\Pages;

use App\Models\SuiteRoom;
use Filament\Pages\Dashboard as BasePage;

class Dashboard extends BasePage
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.dashboard';

    public $widgetData = [];

    public function mount()
    {
        if (auth()->user()->isFrontDesk()) {
            $this->widgetData['availableRooms'] = SuiteRoom::with('room')->where('is_active', true)->where('is_occupied', false)->limit(10)->get();
            $this->widgetData['occupiedRooms'] = SuiteRoom::with('room')->where('is_active', true)->where('is_occupied', true)->limit(10)->get();
        }

        // dd($this->widgetData);
    }
}
