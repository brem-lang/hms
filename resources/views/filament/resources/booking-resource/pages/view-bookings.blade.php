<x-filament-panels::page>
    <h1 style="font-size: 20px;font-weight:bold">{{ $record->room->name }}</h1>
    <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(3, minmax(0, 1fr)); margin-top: -25px;"
        class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">
        <div style="--col-span-default: span 1 / span 1;" class="col-[--col-span-default]">
            <div>
                <div style="--cols-default: repeat(1, minmax(0, 1fr));"
                    class="grid grid-cols-[--cols-default] fi-fo-component-ctn gap-6">
                    <div style="--col-span-default: 1 / -1;" class="col-[--col-span-default]">
                        <section
                            class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                            <div class="fi-section-content-ctn">
                                <div class="fi-section-content p-6">
                                    <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(1, minmax(0, 1fr));"
                                        class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">
                                        <div>
                                            <div>
                                                <img src="{{ asset('suite-photo/' . $record->room->image) }}"
                                                    alt="Image 1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
        <div style="--col-span-default: span 2 / span 2;" class="col-[--col-span-default]">
            <div>
                <div style="--cols-default: repeat(1, minmax(0, 1fr));"
                    class="grid grid-cols-[--cols-default] fi-fo-component-ctn gap-6">

                    <div style="--col-span-default: 1 / -1;" class="col-[--col-span-default]">
                        <section
                            class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                            <div class="fi-section-content-ctn p-6">
                                @if ($record->room_id == 4)
                                    {{ $this->infoListFunctionHall }}
                                @else
                                    {{ $this->infoList }}
                                @endif
                                <br />
                                {{ $this->paymentForm }}
                                <br />
                                <div class="text-right">
                                    @if ($record->status == 'completed')
                                        <x-filament::button size="md" color="primary" disabled>
                                            Booking Accepted
                                        </x-filament::button>
                                    @else
                                        @if ($record->status == 'cancelled')
                                            <x-filament::button size="md" color="danger" disabled>
                                                Booking Cancelled
                                            </x-filament::button>
                                        @else
                                            @if ($record->status == 'done')
                                                {{-- <x-filament::button size="md" color="primary" disabled>
                                                    Booking Accepted
                                                </x-filament::button> --}}
                                            @else
                                                <x-filament::modal id="confirm-modal" width="md" alignment="center"
                                                    icon="heroicon-o-arrow-uturn-left" icon-color="warning">
                                                    <x-slot name="trigger">
                                                        <x-filament::button color="warning"
                                                            icon="heroicon-o-arrow-uturn-left">
                                                            Return
                                                        </x-filament::button>
                                                    </x-slot>
                                                    <x-slot name="heading">
                                                        Return Booking
                                                    </x-slot>

                                                    <x-slot name="description">
                                                        Are you sure you would like to do this?
                                                    </x-slot>

                                                    {{ $this->cancelForm }}

                                                    <x-slot name="footerActions">
                                                        <x-filament::button size="md" color="primary"
                                                            class="w-full" wire:click.prevent="return">
                                                            Confirm
                                                        </x-filament::button>
                                                        <x-filament::button color="gray" outlined size="md"
                                                            class="w-full"
                                                            x-on:click.prevent="$dispatch('close-modal', {id: 'confirm-modal'})">
                                                            Cancel
                                                        </x-filament::button>
                                                    </x-slot>
                                                </x-filament::modal>

                                                {{-- cancel --}}
                                                <x-filament::modal id="confirm-modal" width="md" alignment="center"
                                                    icon="heroicon-o-x-mark" icon-color="danger">
                                                    <x-slot name="trigger">
                                                        <x-filament::button color="danger" icon="heroicon-o-x-mark">
                                                            Cancel
                                                        </x-filament::button>
                                                    </x-slot>
                                                    <x-slot name="heading">
                                                        Cancel Booking
                                                    </x-slot>

                                                    <x-slot name="description">
                                                        Are you sure you would like to do this?
                                                    </x-slot>

                                                    <div class="w-full">
                                                        {{ $this->cancelForm }}
                                                    </div>

                                                    <x-slot name="footerActions">
                                                        <x-filament::button size="md" color="primary"
                                                            class="w-full" wire:click.prevent="cancel">
                                                            Confirm
                                                        </x-filament::button>
                                                        <x-filament::button color="gray" outlined size="md"
                                                            class="w-full"
                                                            x-on:click.prevent="$dispatch('close-modal', {id: 'confirm-modal'})">
                                                            Cancel
                                                        </x-filament::button>
                                                    </x-slot>
                                                </x-filament::modal>

                                                {{-- accept --}}
                                                <x-filament::modal id="confirm-modal" width="md" alignment="center"
                                                    icon="heroicon-o-check" icon-color="success">
                                                    <x-slot name="trigger">
                                                        <x-filament::button icon="heroicon-o-check">
                                                            Accept
                                                        </x-filament::button>
                                                    </x-slot>
                                                    <x-slot name="heading" icon="heroicon-o-check">
                                                        Accept Booking
                                                    </x-slot>

                                                    <x-slot name="description">
                                                        Are you sure you would like to do this?
                                                    </x-slot>

                                                    <x-slot name="footerActions">
                                                        <x-filament::button size="md" color="primary"
                                                            class="w-full" wire:click.prevent="confirm">
                                                            Confirm
                                                        </x-filament::button>
                                                        <x-filament::button color="gray" outlined size="md"
                                                            class="w-full"
                                                            x-on:click.prevent="$dispatch('close-modal', {id: 'confirm-modal'})">
                                                            Cancel
                                                        </x-filament::button>
                                                    </x-slot>
                                                </x-filament::modal>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
