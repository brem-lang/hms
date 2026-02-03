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

                @if ($record->room_id == 4)
                    <div
                        class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">

                        {{-- HEADER --}}
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                                Function Hall Booking Details
                            </h2>

                            {{-- Status Badge --}}
                            @php
                                $statusColors = [
                                    'pending' => 'bg-gray-100 text-gray-700',
                                    'completed' => 'bg-yellow-100 text-yellow-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    'done' => 'bg-green-100 text-green-700',
                                    'returned' => 'bg-red-100 text-red-700',
                                ];
                            @endphp

                            <span
                                class="px-3 py-1 rounded-full text-sm font-medium
            {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $record->status == 'completed' ? 'For CheckIn' : ($record->status == 'done' ? 'Settled' : ucfirst($record->status)) }}
                            </span>
                        </div>

                        {{-- BASIC INFO --}}
                        <table
                            class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                                <tr>
                                    <td class="w-1/4 font-medium text-gray-600 dark:text-gray-300 p-3">First Name</td>
                                    <td class="p-3">{{ $record->organization }}</td>
                                </tr>

                                <tr>
                                    <td class="font-medium text-gray-600 dark:text-gray-300 p-3">Last Name</td>
                                    <td class="p-3">{{ $record->position }}</td>
                                </tr>

                                <tr>
                                    <td class="font-medium text-gray-600 dark:text-gray-300 p-3">Contact Number</td>
                                    <td class="p-3">{{ $record->contact_number }}</td>
                                </tr>

                                <tr>
                                    <td class="font-medium text-gray-600 dark:text-gray-300 p-3">Booking Type</td>
                                    <td class="p-3">
                                        {{ $record->type === 'walkin_booking' ? 'Walk-in' : 'Online' }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        {{-- DATE & TIME --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900">
                                <p class="text-xs text-gray-500">Check In Time</p>
                                <p class="font-semibold">
                                    {{ \Carbon\Carbon::parse($record->check_in_date)->format('F j, Y h:i A') }}
                                </p>
                            </div>

                            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900">
                                <p class="text-xs text-gray-500">Check Out Time</p>
                                <p class="font-semibold">
                                    {{ \Carbon\Carbon::parse($record->check_out_date)->format('F j, Y h:i A') }}
                                </p>
                            </div>

                            <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900">
                                <p class="text-xs text-gray-500">Duration</p>
                                <p class="font-semibold">{{ $record->duration }} hrs</p>
                            </div>
                        </div>

                        {{-- PAYMENT SUMMARY --}}
                        @php
                            $charges = collect($record->additional_charges ?? [])->sum('total_charges');
                            $foodCharges = collect($record->food_charges ?? [])->sum('total_charges');
                        @endphp

                        <table class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg mt-4">
                            <tbody class="divide-y">

                                <tr>
                                    <td class="p-3 font-medium">Room Booking Fee</td>
                                    <td class="p-3 text-right font-semibold">
                                        ₱
                                        {{ number_format(
                                            $record->type !== 'bulk_head_online' ? $record->amount_to_pay : $record->relatedBookings->sum('amount_to_pay'),
                                            2,
                                        ) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="p-3 font-medium">Amount Paid</td>
                                    <td class="p-3 text-right text-green-600 font-semibold">
                                        ₱ {{ number_format($record->amount_paid ?? 0, 2) }}
                                    </td>
                                </tr>

                                <tr class="bg-gray-50 dark:bg-gray-900">
                                    <td class="p-3 font-bold">Balance Due</td>
                                    <td class="p-3 text-right font-bold text-red-600">
                                        ₱ {{ number_format(($record->balance ?? 0) + $charges + $foodCharges, 2) }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        {{-- ROOM INFO --}}
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-1">Room Number(s)</p>
                            <p class="font-semibold">
                                @if ($record->type !== 'bulk_head_online')
                                    {{ ucfirst($record->suiteRoom->name ?? '') }}
                                @else
                                    {{ $record->relatedBookings->pluck('suiteRoom.name')->filter()->map('ucfirst')->implode(', ') }}
                                @endif
                            </p>
                        </div>

                        {{-- NOTES --}}
                        @if ($record->notes)
                            <div class="p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900 mt-4">
                                <p class="text-xs font-semibold text-yellow-700 dark:text-yellow-300">Notes / Requests
                                </p>
                                <p class="mt-1">{{ $record->notes }}</p>
                            </div>
                        @endif

                    </div>
                @else
                    <div
                        class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">

                        {{-- HEADER --}}
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                                    Booking Information
                                </h2>
                                <p class="text-sm text-gray-500">
                                    {{ $record->user->name }}
                                </p>
                            </div>

                            {{-- STATUS BADGE --}}
                            @php
                                $statusColors = [
                                    'pending' => 'bg-gray-100 text-gray-700',
                                    'completed' => 'bg-yellow-100 text-yellow-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    'done' => 'bg-green-100 text-green-700',
                                    'returned' => 'bg-red-100 text-red-700',
                                ];
                            @endphp

                            <span
                                class="px-3 py-1 rounded-full text-sm font-medium
            {{ $statusColors[$record->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $record->status == 'completed' ? 'For Check-In' : ($record->status == 'done' ? 'Settled' : ucfirst($record->status)) }}
                            </span>
                        </div>

                        {{-- BASIC DETAILS --}}
                        <table
                            class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                                <tr>
                                    <td class="w-1/4 font-medium p-3">Guest Name</td>
                                    <td class="p-3">{{ $record->user->name }}</td>
                                </tr>

                                <tr>
                                    <td class="font-medium p-3">Contact Number</td>
                                    <td class="p-3">{{ $record->user->contact_number }}</td>
                                </tr>

                                <tr>
                                    <td class="font-medium p-3">Booking Type</td>
                                    <td class="p-3">
                                        {{ $record->type === 'walkin_booking' ? 'Walk-in' : 'Online' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="font-medium p-3">Date of Booking</td>
                                    <td class="p-3">
                                        {{ \Carbon\Carbon::parse($record->created_at)->timezone('Asia/Manila')->format('F j, Y h:i A') }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        {{-- DATE & TIME --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                                <p class="text-xs text-gray-500">Check In Time</p>
                                <p class="font-semibold">
                                    {{ \Carbon\Carbon::parse($record->check_in_date)->format('F j, Y h:i A') }}
                                </p>
                            </div>

                            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                                <p class="text-xs text-gray-500">Check Out Time</p>
                                <p class="font-semibold">
                                    {{ \Carbon\Carbon::parse($record->check_out_date)->format('F j, Y h:i A') }}
                                </p>
                            </div>

                            <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
                                <p class="text-xs text-gray-500">Total Days</p>
                                <p class="font-semibold">{{ $record->days }}</p>
                            </div>
                        </div>

                        {{-- PERSON COUNT --}}
                        <table class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg mt-4">
                            <tbody class="divide-y">

                                <tr>
                                    <td class="p-3 font-medium">Number of Persons</td>
                                    <td class="p-3 text-right">
                                        {{ $record->type != 'bulk_head_online' ? $record->no_persons : $record->relatedBookings->sum('no_persons') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="p-3 font-medium">Additional Persons</td>
                                    <td class="p-3 text-right">
                                        {{ $record->type != 'bulk_head_online'
                                            ? $record->additional_persons
                                            : $record->relatedBookings->sum('additional_persons') }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        {{-- PAYMENT SUMMARY --}}
                        @php
                            $extraCharges = collect($record->additional_charges ?? [])->sum('total_charges');
                            $foodCharges = collect($record->food_charges ?? [])->sum('total_charges');
                        @endphp

                        <table class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg mt-4">
                            <tbody class="divide-y">

                                <tr>
                                    <td class="p-3 font-medium">Room Booking Fee</td>
                                    <td class="p-3 text-right font-semibold">
                                        ₱
                                        {{ number_format(
                                            $record->type != 'bulk_head_online' ? $record->amount_to_pay : $record->relatedBookings->sum('amount_to_pay'),
                                            2,
                                        ) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="p-3 font-medium">Amount Paid</td>
                                    <td class="p-3 text-right text-green-600 font-semibold">
                                        ₱ {{ number_format($record->amount_paid ?? 0, 2) }}
                                    </td>
                                </tr>

                                <tr class="bg-gray-50 dark:bg-gray-900">
                                    <td class="p-3 font-bold">Balance Due</td>
                                    <td class="p-3 text-right font-bold text-red-600">
                                        ₱ {{ number_format(($record->balance ?? 0) + $extraCharges + $foodCharges, 2) }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>

                        {{-- ROOM DETAILS --}}
                        <div class="mt-4">
                            <p class="text-sm text-gray-500 mb-1">Room Number(s)</p>
                            <p class="font-semibold">
                                @if ($record->type !== 'bulk_head_online')
                                    {{ ucfirst($record->suiteRoom->name ?? '') }}
                                @else
                                    {{ $record->relatedBookings->pluck('suiteRoom.name')->filter()->map('ucfirst')->implode(', ') }}
                                @endif
                            </p>
                        </div>

                        {{-- NOTES --}}
                        @if ($record->notes)
                            <div class="p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900 mt-4">
                                <p class="text-xs font-semibold text-yellow-700 dark:text-yellow-300">
                                    Notes / Requests
                                </p>
                                <p class="mt-1">{{ $record->notes }}</p>
                            </div>
                        @endif

                    </div>

                @endif

                <div style="--cols-default: repeat(1, minmax(0, 1fr));margin-top: 20px;"
                    class="grid grid-cols-[--cols-default] fi-fo-component-ctn gap-6">
                    <div style="--col-span-default: 1 / -1;" class="col-[--col-span-default]">
                        <section
                            class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                            <div class="fi-section-content-ctn p-6">
                                {{ $this->form }}
                                <br />
                                <div class="text-right">
                                    @if ($record->is_occupied)
                                        <x-filament::button size="md" color="success" disabled>
                                            Already Checked In
                                        </x-filament::button>
                                    @else
                                        <x-filament::button size="md" color="primary" wire:click="checkIn">
                                            Check In
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
