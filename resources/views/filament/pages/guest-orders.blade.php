<x-filament-panels::page>
    @foreach ($foods as $food)
        <h1 style="font-size: 20px;font-weight:bold">{{ $food->name }}</h1>
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
                                                    <img src="{{ asset('food-photo/' . $food->image) }}" alt="Image 1"
                                                        style="width: 400px; height: 250px;">
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
                                <div class="fi-section-content-ctn">
                                    <div class="fi-section-content p-6">
                                        <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));"
                                            class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">

                                            <div style="font-size: 14px; display: flex;width: 100%;">
                                                <div class="column" style="padding: 0 10px; word-break: break-word;">
                                                    <h1>Description</h1>
                                                    {{ $food->description }}
                                                </div>
                                            </div>
                                            <div style="font-size: 14px; display: flex;width: 100%;">
                                                <div class="column"
                                                    style="flex: auto; padding: 0 10px; word-break: break-word;">
                                                    <h1>Price</h1>
                                                    <ul class="features-list"
                                                        style="list-style-type: none; padding: 0;">
                                                        <li>₱ {{ $food->price }} - per serving </li>
                                                    </ul>
                                                </div>
                                                <div class="column"
                                                    style="flex: auto; padding: 0 10px; word-break: break-word;">
                                                    @if ($food->is_available)
                                                        <x-filament::modal width="4xl">
                                                            <x-slot name="trigger">
                                                                <x-filament::button>
                                                                    Order Now
                                                                </x-filament::button>
                                                            </x-slot>
                                                            {{ $this->form }}

                                                            <x-slot name="footerActions">
                                                                <x-filament::button
                                                                    wire:click.prevent.once="foodOrder({{ $food->id }})"
                                                                    wire:loading.attr="disabled">
                                                                    Submit
                                                                </x-filament::button>
                                                            </x-slot>
                                                        </x-filament::modal>
                                                    @else
                                                        <x-filament::button color="danger" disabled>
                                                            Not Available
                                                        </x-filament::button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</x-filament-panels::page>
