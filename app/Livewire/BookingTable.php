<?php

namespace App\Livewire;

use App\Models\Booking;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BookingTable extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.booking-table');
    }

    #[Url(except: '')]
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed()] #[On('rerender')]
    public function data()
    {
        return Booking::query()
            ->when($this->search, fn ($query) => $query->whereHas('room', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')))
            ->where('user_id', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->paginate(10);
    }
}
