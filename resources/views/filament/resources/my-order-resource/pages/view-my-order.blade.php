<x-filament-panels::page>
    <h1 style="font-size: 20px;font-weight:bold">You Order {{ $record->food->name }}</h1>
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
                                                <img src="{{ asset('food-photo/' . $record->food->image) }}"
                                                    alt="Image 1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                    <div style="--col-span-default: 1 / -1;" class="col-[--col-span-default]">
                        <section
                            class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                            <div class="fi-section-content-ctn">
                                <div class="fi-section-content p-6">
                                    <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(1, minmax(0, 1fr));"
                                        class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">
                                        <div>
                                            Scan to Pay
                                            <div>
                                                <img src="{{ asset('images/qrcode.png') }}" alt="Image 1">
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
                                {{-- <div class="fi-section-content p-6">
                                    <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));"
                                        class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">

                                        <div style="font-size: 14px; display: flex;width: 100%;">
                                            <div class="column" style="padding: 0 10px; word-break: break-word;">
                                                <h1>Rates</h1>
                                                <ul class="features-list" style="list-style-type: none; padding: 0;">
                                                    <li>₱ 300.00 - 3 hours stay</li>
                                                    <li>₱ 500.00 - 6 hours stay</li>
                                                    <li>₱ 800.00 - 12 hours stay</li>
                                                    <li>₱ 1200.00 - Overnight stay</li>
                                                    <li>₱ 100.00 - Extension / hour</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div style="font-size: 14px; display: flex;width: 100%;">
                                            <div class="column"
                                                style="flex: auto; padding: 0 10px; word-break: break-word;">
                                                <h1>Amenities</h1>
                                                <ul class="features-list" style="list-style-type: none; padding: 0;">
                                                    <li><i class="fas fa-check-circle"></i> Airconditioned Room</li>
                                                    <li><i class="fas fa-check-circle"></i> Essential Kit</li>
                                                    <li><i class="fas fa-check-circle"></i> Complimentary Bottled Water
                                                    </li>
                                                    <li><i class="fas fa-check-circle"></i> Parking space</li>
                                                    <li><i class="fas fa-check-circle"></i> Fire Alarm</li>
                                                </ul>
                                            </div>
                                            <div class="column"
                                                style="flex: auto; padding: 0 10px; word-break: break-word;">
                                                <x-filament::modal width="4xl">
                                                    <x-slot name="trigger">
                                                        <x-filament::button>
                                                            Book Now
                                                        </x-filament::button>
                                                    </x-slot>
                                                    {{ $this->standardSuiteForm }}

                                                    <x-slot name="footerActions">
                                                        <x-filament::button wire:click.prevent="standardSuiteSubmit"
                                                            wire:loading.attr="disabled">
                                                            Submit
                                                        </x-filament::button>
                                                    </x-slot>
                                                </x-filament::modal>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}
                                {{ $this->infoList }}
                                <br />
                                {{-- {{ $this->notesInfoList }} --}}
                                <br />
                                {{ $this->form }}
                                <br />
                                <div class="text-right">
                                    @if ($record->status == 'pending')
                                        @if ($record->proof_of_payment)
                                            <x-filament::button size="md" color="primary" disabled>
                                                Waiting for Confirmation...
                                            </x-filament::button>
                                        @else
                                            <x-filament::modal id="confirm-modal" width="md" alignment="center"
                                                icon="heroicon-o-check" icon-color="success">
                                                <x-slot name="trigger">
                                                    <x-filament::button>
                                                        Confirm Payment
                                                    </x-filament::button>
                                                </x-slot>
                                                <x-slot name="heading">
                                                    Confirm Payment
                                                </x-slot>

                                                <x-slot name="description">
                                                    Are you sure you would like to do this?
                                                </x-slot>

                                                <x-slot name="footerActions">
                                                    <x-filament::button size="md" color="primary" class="w-full"
                                                        wire:click.prevent="pay">
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
                                    @else
                                        <x-filament::button size="md" color="primary" disabled>
                                            Completed
                                        </x-filament::button>
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
