<x-filament-panels::page>
    <div>
        @livewire(\App\Livewire\RoomsOverview::class)
    </div>

    {{ $this->table }}
</x-filament-panels::page>
