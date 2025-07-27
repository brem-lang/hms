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
                                                <h1 style="font-weight: 700">
                                                    {{ $record['functionHallOccupied'] }} Available Room(s)
                                                </h1>
                                                <br>
                                                @if (auth()->user()->isFrontDesk())
                                                    @if ($record['functionHallOccupied'] > 0)
                                                        @if ($record['functionHall']['status'])
                                                            <x-filament::modal width="4xl">
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
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </section>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <div id="calendar" class="p-4 bg-white rounded-lg shadow" x-data="{
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
