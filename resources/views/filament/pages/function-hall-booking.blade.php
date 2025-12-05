<x-filament-panels::page>
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
        @if ($activePage == 'home')
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
                                                    <ul class="features-list"
                                                        style="list-style-type: none; padding: 0;">
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
                                                    <ul class="features-list"
                                                        style="list-style-type: none; padding: 0;">
                                                        <li><i class="fas fa-check-circle"></i> 4 hours Rental</li>
                                                        <li><i class="fas fa-check-circle"></i> Airconditioned Room</li>
                                                        <li><i class="fas fa-check-circle"></i> Basic SoundSystem
                                                        </li>
                                                        <li><i class="fas fa-check-circle"></i> Standby Generator</li>
                                                        <li><i class="fas fa-check-circle"></i> Good for 40 pax</li>
                                                    </ul>
                                                </div>
                                                <div class="column"
                                                    style="flex: auto; padding: 0 10px; word-break: break-word;">
                                                    {{-- <h1 style="font-weight: 700">
                                                    {{ $record['functionHallOccupied'] }} Available Room(s)
                                                </h1> --}}
                                                    <br>
                                                    @if (auth()->user()->isFrontDesk())
                                                        @if ($record['functionHallOccupied'] > 0)
                                                            @if ($record['functionHall']['status'])
                                                                <x-filament::button wire:click.prevent.once="book">
                                                                    Book Now
                                                                </x-filament::button>
                                                                {{-- <x-filament::modal width="4xl">
                                                                <x-slot name="trigger">
                                                                    <x-filament::button>
                                                                        Book Now
                                                                    </x-filament::button>
                                                                </x-slot>
                                                                {{ $this->form }}

                                                                <x-slot name="footerActions">
                                                                    <x-filament::button
                                                                        wire:click.prevent.once="functionHallSuiteSubmit"
                                                                        wire:loading.attr="disabled">
                                                                        Submit
                                                                    </x-filament::button>
                                                                </x-slot>
                                                            </x-filament::modal> --}}
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
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            </section>
                        </div>
                    </div>

                    <div style="margin-top: 20px;">
                        {{-- <div id="calendar" class="p-4 bg-white rounded-lg shadow" x-data="{
                        events: [{
                                title: 'Wedding Reception',
                                start: '2025-07-28T14:00:00',
                                end: '2025-07-28T18:00:00',
                                color: '#2563eb' // Blue
                            },
                            {
                                title: 'Corporate Seminar',
                                start: '2025-07-29T09:00:00',
                                end: '2025-07-29T17:00:00',
                                color: '#16a34a' // Green
                            },
                            {
                                title: 'Birthday Party',
                                start: '2025-07-30T18:00:00',
                                end: '2025-07-30T22:00:00',
                                color: '#dc2626' // Red
                            }
                        ],
                    
                        initCalendar() {
                            const calendarEl = document.getElementById('calendar');
                            const calendar = new FullCalendar.Calendar(calendarEl, {
                                initialView: 'dayGridMonth',
                                headerToolbar: {
                                    left: 'prev,next today',
                                    center: 'title',
                                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                                },
                                events: this.events
                            });
                            calendar.render();
                        }
                    }"
                        x-init="initCalendar()">
                    </div> --}}
                        <div class="p-4 bg-white rounded-lg shadow" x-data="fullCalendarComponent()" x-init="initCalendar();
                        Livewire.hook('element.updated', () => { $nextTick(() => initCalendar()) });">
                            <div x-ref="fullcalendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($activePage == 'book')
            <div style="--col-span-default: span 2 / span 2;" class="col-[--col-span-default]">
                <div>
                    <div style="--cols-default: repeat(1, minmax(0, 1fr));"
                        class="grid grid-cols-[--cols-default] fi-fo-component-ctn gap-6">
                        <div style="--col-span-default: 1 / -1;" class="col-[--col-span-default]">
                            {{ $this->form }}
                            @php
                                $amount_paid = $this->corkageValue + $this->packageValue + $this->typeValue;
                            @endphp
                            <x-filament::section style="margin-top: 20px;"
                                class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                                <div class="fi-section-content-ctn">
                                    <div class="fi-section-content p-1">
                                        <div style="--cols-default: repeat(1, minmax(0, 1fr)); --cols-lg: repeat(2, minmax(0, 1fr));"
                                            class="grid grid-cols-[--cols-default] lg:grid-cols-[--cols-lg] fi-fo-component-ctn gap-6">
                                            <div style="--col-span-default: span 1 / span 1;"
                                                class="col-[--col-span-default]">
                                                <div data-field-wrapper="" class="fi-fo-field-wrp">
                                                    <div class="grid gap-y-2">
                                                        <div class="flex items-center gap-x-3 justify-between ">
                                                            <label
                                                                class="fi-fo-field-wrp-label inline-flex items-center gap-x-3"
                                                                for="data.total_amount">
                                                                <span
                                                                    class="text-sm font-medium leading-6 text-gray-950 dark:text-white">

                                                                    Total
                                                                    Amount
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="grid auto-cols-fr gap-y-2">
                                                            <div
                                                                class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500 fi-fo-text-input overflow-hidden">

                                                                <div
                                                                    class="fi-input-wrp-prefix items-center gap-x-3 ps-3 flex border-e border-gray-200 pe-3 ps-3 dark:border-white/10">
                                                                    <span
                                                                        class="fi-input-wrp-label whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                                        ₱
                                                                    </span>
                                                                </div>
                                                                <div class="fi-input-wrp-input min-w-0 flex-1">
                                                                    <input
                                                                        class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0 ps-3 pe-3"
                                                                        id="data.total_amount" readonly="readonly"
                                                                        value="{{ number_format($amount_paid, 2) }}"
                                                                        type="text">
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div style="--col-span-default: span 1 / span 1;"
                                                class="col-[--col-span-default]">
                                                <div data-field-wrapper="" class="fi-fo-field-wrp">
                                                    <div class="grid gap-y-2">
                                                        <div class="flex items-center gap-x-3 justify-between ">
                                                            <label
                                                                class="fi-fo-field-wrp-label inline-flex items-center gap-x-3"
                                                                for="data.50_percent_down_payment">
                                                                <span
                                                                    class="text-sm font-medium leading-6 text-gray-950 dark:text-white">

                                                                    50% Down
                                                                    Payment
                                                                </span>
                                                            </label>
                                                        </div>
                                                        <div class="grid auto-cols-fr gap-y-2">
                                                            <div
                                                                class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 ring-gray-950/10 dark:ring-white/20 [&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&amp;:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500 fi-fo-text-input overflow-hidden">

                                                                <div
                                                                    class="fi-input-wrp-prefix items-center gap-x-3 ps-3 flex border-e border-gray-200 pe-3 ps-3 dark:border-white/10">
                                                                    <span
                                                                        class="fi-input-wrp-label whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                                        ₱
                                                                    </span>
                                                                </div>
                                                                <div class="fi-input-wrp-input min-w-0 flex-1">
                                                                    <input
                                                                        class="fi-input block w-full border-none py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0 ps-3 pe-3"
                                                                        id="data.50_percent_down_payment"
                                                                        readonly="readonly" type="text"
                                                                        value="{{ number_format($amount_paid / 2, 2) }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-filament::section>
                            <div class="mt-3 text-right">
                                <x-filament::button wire:click.prevent="functionHallSuiteSubmit"
                                    wire:loading.attr="disabled">
                                    Submit
                                </x-filament::button>

                                <x-filament::button wire:click.prevent.once="cancel" wire:loading.attr="disabled"
                                    color="danger">
                                    Cancel
                                </x-filament::button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
<script>
    function fullCalendarComponent() {
        return {
            events: @json($this->calendarEvents),

            calendar: null,

            initCalendar() {
                if (this.calendar) {
                    this.calendar.destroy();
                }

                const calendarEl = this.$refs.fullcalendar;

                this.calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: this.events
                });
                this.calendar.render();
            }
        }
    }
</script>
