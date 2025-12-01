<x-filament-panels::page>

    {{-- <nav class="fi-tabs flex max-w-full gap-x-1 overflow-x-auto mx-auto rounded-xl bg-white p-2 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
        role="tablist">
        <button type="button"
            class="fi-tabs-item group flex items-center justify-center gap-x-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75  hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5 "
            aria-selected="aria-selected" role="tab" wire:click="setTab('all')">
            <span class="fi-tabs-item-label transition duration-75 text-primary-600 dark:text-primary-400">
                All
            </span>
        </button>
        <button type="button"
            class="fi-tabs-item group flex items-center justify-center gap-x-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
            role="tab" wire:click="setTab('checkIn')">
            <span
                class="fi-tabs-item-label transition duration-75 text-gray-500 group-hover:text-gray-700 group-focus-visible:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-200 dark:group-focus-visible:text-gray-200">
                CheckIn
            </span>
        </button>
        <button type="button"
            class="fi-tabs-item group flex items-center justify-center gap-x-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium outline-none transition duration-75 hover:bg-gray-50 focus-visible:bg-gray-50 dark:hover:bg-white/5 dark:focus-visible:bg-white/5"
            role="tab" wire:click="setTab('checkOut')">
            <span
                class="fi-tabs-item-label transition duration-75 text-gray-500 group-hover:text-gray-700 group-focus-visible:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-200 dark:group-focus-visible:text-gray-200">
                CheckOut
            </span>
        </button>
    </nav> --}}

    {{ $this->table }}
</x-filament-panels::page>
