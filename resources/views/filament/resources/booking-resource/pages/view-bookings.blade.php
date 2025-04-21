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
                                {{ $this->infoList }}
                                <br />
                                {{ $this->form }}
                                <br />
                                <div class="text-right">
                                    @if ($record->status == 'completed')
                                        <x-filament::button size="md" color="primary" disabled>
                                            Booking Confirm
                                        </x-filament::button>
                                    @else
                                        <x-filament::modal id="confirm-modal" width="md" alignment="center"
                                            icon="heroicon-o-check" icon-color="success">
                                            <x-slot name="trigger">
                                                <x-filament::button>
                                                    Confirm Booking
                                                </x-filament::button>
                                            </x-slot>
                                            <x-slot name="heading">
                                                Confirm Booking
                                            </x-slot>

                                            <x-slot name="description">
                                                Are you sure you would like to do this?
                                            </x-slot>

                                            <x-slot name="footerActions">
                                                <x-filament::button size="md" color="primary" class="w-full"
                                                    wire:click.prevent="confirm">
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
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
