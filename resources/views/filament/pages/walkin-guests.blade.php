<x-filament-panels::page>
    {{-- //standar --}}
    <h1 style="font-size: 20px;font-weight:bold">Standard Suite</h1>
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
                                                <img src="{{ asset('suite-photo/' . $record['standard']['image']) }}"
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
                            <div class="fi-section-content-ctn">
                                <div class="fi-section-content p-6">
                                    <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));"
                                        class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">

                                        <div style="font-size: 14px; display: flex;width: 100%;">
                                            <div class="column" style="padding: 0 10px; word-break: break-word;">
                                                <h1>Rates</h1>
                                                @foreach ($record['standard']['items'] ?? [] as $item)
                                                    <ul class="features-list"
                                                        style="list-style-type: none; padding: 0;">
                                                        <li>₱ {{ $item['price'] }} - {{ $item['hours'] }}</li>
                                                    </ul>
                                                @endforeach
                                                {{-- <ul class="features-list" style="list-style-type: none; padding: 0;">
                                                    <li>₱ 300.00 - 3 hours stay</li>
                                                    <li>₱ 500.00 - 6 hours stay</li>
                                                    <li>₱ 800.00 - 12 hours stay</li>
                                                    <li>₱ 1200.00 - Overnight stay</li>
                                                    <li>₱ 100.00 - Extension / hour</li>
                                                    <li>₱ 700.00 - Extra Person w/ Extra Bed</li>
                                                </ul> --}}
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
                                                    <li><i class="fas fa-check-circle"></i> Good for 2 pax</li>
                                                </ul>
                                            </div>
                                            <div class="column"
                                                style="flex: auto; padding: 0 10px; word-break: break-word;">
                                                @if ($record['standard']['available_rooms'] > 0)
                                                    @if ($record['standard']['status'])
                                                        <x-filament::modal width="4xl">
                                                            <x-slot name="trigger">
                                                                <x-filament::button>
                                                                    Book Now
                                                                </x-filament::button>
                                                            </x-slot>
                                                            {{ $this->standardSuiteForm }}

                                                            <x-slot name="footerActions">
                                                                <x-filament::button
                                                                    wire:click.prevent="standardSuiteSubmit"
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
    {{-- deluxe --}}
    <h1 style="font-size: 20px;font-weight:bold">Deluxe Suite</h1>
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
                                                <img src="{{ asset('suite-photo/' . $record['deluxe']['image']) }}"
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
                            <div class="fi-section-content-ctn">
                                <div class="fi-section-content p-6">
                                    <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));"
                                        class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">

                                        <div style="font-size: 14px; display: flex;width: 100%;">
                                            <div class="column" style="padding: 0 10px; word-break: break-word;">
                                                <h1>Rates</h1>
                                                @foreach ($record['deluxe']['items'] ?? [] as $item)
                                                    <ul class="features-list"
                                                        style="list-style-type: none; padding: 0;">
                                                        <li>₱ {{ $item['price'] }} - {{ $item['hours'] }}</li>
                                                    </ul>
                                                @endforeach
                                                {{-- <ul class="features-list" style="list-style-type: none; padding: 0;">
                                                    <li>₱ 350.00 - 3 hours stay</li>
                                                    <li>₱ 550.00 - 6 hours stay</li>
                                                    <li>₱ 850.00 - 12 hours stay</li>
                                                    <li>₱ 1400.00 - Overnight stay</li>
                                                    <li>₱ 100.00 - Extension / hour</li>
                                                    <li>₱ 700.00 - Extra Person w/ Extra Bed</li>
                                                </ul> --}}
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
                                                    <li><i class="fas fa-check-circle"></i> Good for 2 pax</li>
                                                </ul>
                                            </div>
                                            <div class="column"
                                                style="flex: auto; padding: 0 10px; word-break: break-word;">

                                                @if ($record['deluxe']['available_rooms'] > 0)
                                                    @if ($record['deluxe']['status'])
                                                        <x-filament::modal width="4xl">
                                                            <x-slot name="trigger">
                                                                <x-filament::button>
                                                                    Book Now
                                                                </x-filament::button>
                                                            </x-slot>
                                                            {{ $this->deluxeSuiteForm }}

                                                            <x-slot name="footerActions">
                                                                <x-filament::button
                                                                    wire:click.prevent="deluxeSuiteSubmit"
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
    {{-- executive --}}
    <h1 style="font-size: 20px;font-weight:bold">Executive Suite</h1>
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
                                                <img src="{{ asset('suite-photo/' . $record['executive']['image']) }}"
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
                            <div class="fi-section-content-ctn">
                                <div class="fi-section-content p-6">
                                    <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));"
                                        class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">

                                        <div style="font-size: 14px; display: flex;width: 100%;">
                                            <div class="column" style="padding: 0 10px; word-break: break-word;">
                                                <h1>Rates</h1>
                                                @foreach ($record['executive']['items'] ?? [] as $item)
                                                    <ul class="features-list"
                                                        style="list-style-type: none; padding: 0;">
                                                        <li>₱ {{ $item['price'] }} - {{ $item['hours'] }}</li>
                                                    </ul>
                                                @endforeach
                                                {{-- <ul class="features-list" style="list-style-type: none; padding: 0;">
                                                    <li>₱ 400.00 - 3 hours stay</li>
                                                    <li>₱ 600.00 - 6 hours stay</li>
                                                    <li>₱ 900.00 - 12 hours stay</li>
                                                    <li>₱ 1600.00 - Overnight stay</li>
                                                    <li>₱ 150.00 - Extension / hour</li>
                                                    <li>₱ 700.00 - Extra Person w/ Extra Bed</li>
                                                </ul> --}}
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
                                                    <li><i class="fas fa-check-circle"></i> Good for 2 pax</li>
                                                </ul>
                                            </div>
                                            <div class="column"
                                                style="flex: auto; padding: 0 10px; word-break: break-word;">
                                                @if ($record['executive']['available_rooms'] > 0)
                                                    @if ($record['executive']['status'])
                                                        <x-filament::modal width="4xl">
                                                            <x-slot name="trigger">
                                                                <x-filament::button>
                                                                    Book Now
                                                                </x-filament::button>
                                                            </x-slot>
                                                            {{ $this->executiveSuiteForm }}

                                                            <x-slot name="footerActions">
                                                                <x-filament::button
                                                                    wire:click.prevent="executiveSuiteSubmit"
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
    {{-- function hall --}}
    <h1 style="font-size: 20px;font-weight:bold">Function Hall</h1>
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
                                                <img src="{{ asset('suite-photo/' . $record['functionHall']['image']) }}"
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
                            <div class="fi-section-content-ctn">
                                <div class="fi-section-content p-6">
                                    <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));"
                                        class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">
                                        <div style="font-size: 14px; display: flex;width: 100%;">
                                            <div class="column" style="padding: 0 10px; word-break: break-word;">
                                                <h1>Venue</h1>
                                                <ul class="features-list" style="list-style-type: none; padding: 0;">
                                                    @foreach ($record['functionHall']['suite_rooms'] as $item)
                                                        <li>{{ $item->name }} - ₱ {{ $item->price }}</li>
                                                    @endforeach
                                                    <li>₱ 1000.00 - Extension / hour</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div style="font-size: 14px; display: flex;width: 100%;">
                                            <div class="column"
                                                style="flex: auto; padding: 0 10px; word-break: break-word;">
                                                <h1>Amenities</h1>
                                                <ul class="features-list" style="list-style-type: none; padding: 0;">
                                                    <li><i class="fas fa-check-circle"></i> 4 hours Rental</li>
                                                    <li><i class="fas fa-check-circle"></i> Airconditioned Room</li>
                                                    <li><i class="fas fa-check-circle"></i> Basic SoundSystem
                                                    </li>
                                                    <li><i class="fas fa-check-circle"></i> Standby Generator</li>
                                                    <li><i class="fas fa-check-circle"></i> Good for 30 pax</li>
                                                </ul>
                                            </div>
                                            <div class="column"
                                                style="flex: auto; padding: 0 10px; word-break: break-word;">
                                                @if ($record['functionHall']['available_rooms'] > 0)
                                                    @if ($record['executive']['status'])
                                                        <x-filament::modal width="4xl">
                                                            <x-slot name="trigger">
                                                                <x-filament::button>
                                                                    Book Now
                                                                </x-filament::button>
                                                            </x-slot>
                                                            {{ $this->functionHallForm }}

                                                            <x-slot name="footerActions">
                                                                <x-filament::button
                                                                    wire:click.prevent="functionHallSuiteSubmit"
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Container holding the images */
        .slider-container {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin: auto;
            overflow: hidden;
        }

        /* Slides */
        .slides {
            display: flex;
            transition: transform 0.5s ease;
        }

        .slide {
            min-width: 100%;
            box-sizing: border-box;
        }

        .slide img {
            width: 100%;
            height: auto;
        }

        /* Navigation dots */
        .dots-container {
            text-align: center;
            position: absolute;
            bottom: 10px;
            width: 100%;
        }

        .dot {
            display: inline-block;
            height: 12px;
            width: 12px;
            margin: 0 5px;
            background-color: #bbb;
            border-radius: 50%;
            cursor: pointer;
        }

        .active-dot {
            background-color: #717171;
        }

        /* Previous and next buttons */
        .prev,
        .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 18px;
        }

        .prev {
            left: 0;
        }

        .next {
            right: 0;
        }

        .prev:hover,
        .next:hover {
            background-color: rgba(0, 0, 0, 0.7);
        }
    </style>
</x-filament-panels::page>
<script>
    let slideIndex = 1;
    showSlides(slideIndex);

    // Next/previous controls
    function moveSlide(n) {
        showSlides(slideIndex += n);
    }

    // Dots controls
    function currentSlide(n) {
        showSlides(slideIndex = n);
    }

    function showSlides(n) {
        let slides = document.querySelectorAll('.slide');
        let dots = document.querySelectorAll('.dot');

        if (n > slides.length) {
            slideIndex = 1;
        }
        if (n < 1) {
            slideIndex = slides.length;
        }

        // Hide all slides
        slides.forEach(slide => {
            slide.style.display = "none";
        });

        // Remove active class from all dots
        dots.forEach(dot => {
            dot.classList.remove('active-dot');
        });

        // Show the current slide and add active class to the corresponding dot
        slides[slideIndex - 1].style.display = "block";
        dots[slideIndex - 1].classList.add('active-dot');
    }
</script>
