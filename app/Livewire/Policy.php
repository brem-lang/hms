<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Policy extends Component
{
    public $notifications;

    public function render()
    {
        return view('livewire.policy');
    }

    public function mount()
    {
        if (Auth::check() && ! auth()->user()->isCustomer()) {
            abort(404);
        }

        if (Auth::check()) {
            $this->loadNotifications();
        }
    }

    public function loadNotifications()
    {
        $this->notifications = auth()->user()
            ->unreadNotifications()
            ->take(10)
            ->get();
    }
}
