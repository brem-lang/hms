<?php

namespace App\Livewire;

use App\Models\Booking;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MyBooking extends Component
{
    use WithPagination;

    public $notifications;

    public function render()
    {
        return view('livewire.my-booking');
    }

    #[Computed()] #[On('rerender')]
    public function data()
    {
        return Booking::query()
            ->orderBy('id', 'desc')
            ->paginate(2);
    }

    public function mount()
    {
        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->route('my-bookings');
    }

    public function clearAll()
    {
        auth()->user()->notifications()->delete();

        return redirect()->route('my-bookings');
    }

    public function loadNotifications()
    {
        $this->notifications = auth()->user()
            ->unreadNotifications()
            ->take(10)
            ->get();
    }
}
