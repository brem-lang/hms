<div>
    <!-- header-start -->
    <header>
        <div class="header-area ">
            <div id="sticky-header" class="main-header-area">
                <div class="container-fluid p-0">
                    <div class="row align-items-center no-gutters">
                        <div class="col-xl-5 col-lg-6">
                            <div class="main-menu  d-none d-lg-block">
                                <nav>
                                    <ul id="navigation">
                                        <li><a class="" href="{{ route('index') }}">home</a></li>
                                        <li><a class="active" href="{{ route('my-bookings') }}">my bookings</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-2">
                            <div class="logo-img">
                                <a href="{{ route('index') }}">
                                    <img src="img/logo.png" alt="">
                                </a>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-4 d-none d-lg-block">
                            <div class="book_room">
                                <div class="socail_links">
                                    <ul>
                                        <li>
                                            <a href="https://www.facebook.com/MilleniumSuitesPanabo">
                                                <i class="fa fa-facebook-square"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="book_btn d-none d-lg-block">
                                    @auth
                                        <a wire:click.prevent="logout">Logout</a>
                                    @endauth

                                    @guest
                                        <a href="login">Login</a>
                                    @endguest
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- header-end -->
    <!-- bradcam_area_start -->
    <div class="bradcam_area"
        style="background-image: url('{{ asset('suite-photo/' . $booking->room->image) }}'); background-size: cover; background-position: center; height: 100%;">
        <h3>{{ $booking->room->name }}</h3>
    </div>

    <!-- bradcam_area_end -->

    {{-- data --}}
    <div class="about_area" style="margin-top: -120px;">
        <div class="container">
            <div class="mb-5">
                {{-- <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="mb-0">Booking Information - {{ $booking->booking_number }}</h3>
                    <x-filament::modal id="mail-modal">
                        <x-slot name="trigger">
                            <x-filament::button color="success" class="mb-5">
                                Mail
                            </x-filament::button>
                        </x-slot>

                        <Textarea label="Reason for cancellation" wire:model.defer="reason"></Textarea>

                        <x-slot name="footerActions">
                            <a href="#" class="genric-btn info w-full" wire:click="cancel">Confirm</a>
                            <a href="#" class="genric-btn danger w-full"
                                x-on:click.prevent="$dispatch('close-modal', {id: 'mail-modal'})">Cancel</a>
                        </x-slot>
                    </x-filament::modal>
                </div> --}}


                {{-- <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> {{ $booking->user->name }}</p>
                        <p><strong>Contact Number:</strong> {{ $booking->user->contact_number }}</p>
                        <p><strong>Status:</strong>
                            <span
                                class="badge badge-{{ $booking->status == 'done' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : ($booking->status == 'completed' ? 'warning' : 'secondary')) }}">
                                {{ $booking->status == 'completed' ? 'For CheckIn' : ($booking->status == 'done' ? 'Settled' : ucfirst($booking->status)) }}
                            </span>
                        </p>
                        @if ($booking->room_id != 4)
                            <p><strong>Start Date:</strong>
                                {{ \Carbon\Carbon::parse($booking->start_date)->format('F j, Y h:i A') }}</p>
                            <p><strong>End Date:</strong>
                                {{ \Carbon\Carbon::parse($booking->end_date)->format('F j, Y h:i A') }}</p>
                        @endif
                        <p><strong>Date of Booking:</strong>
                            {{ \Carbon\Carbon::parse($booking->created_at)->timezone('Asia/Manila')->format('F j, Y h:i A') }}
                        </p>
                        <p><strong>Days:</strong> {{ $booking->days }}</p>
                        <p><strong>Duration Hrs:</strong> {{ $booking->duration }}</p>

                        @if ($booking->status == 'returned')
                            <p><strong>Notes:</strong> {{ $booking->return_notes }}</p>
                        @endif

                        @if ($booking->status == 'cancelled')
                            <p><strong>Notes:</strong> {{ $booking->cancel_reason }}</p>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <p><strong>Number of Persons:</strong>
                            {{ $booking->type != 'bulk_head_online' ? $booking->no_persons : $booking->relatedBookings->sum('no_persons') }}
                        </p>
                        <p><strong>Additional Persons:</strong>
                            {{ $booking->type != 'bulk_head_online' ? $booking->additional_persons : $booking->relatedBookings->sum('additional_persons') }}
                        </p>
                        <p><strong>Check In Time:</strong>
                            {{ \Carbon\Carbon::parse($booking->check_in_date)->format('F j, Y h:i A') }}</p>
                        <p><strong>Check Out Time:</strong>
                            {{ \Carbon\Carbon::parse($booking->check_out_date)->format('F j, Y h:i A') }}</p>
                        <p><strong>Amount:</strong> ₱
                            {{ $booking->type != 'bulk_head_online' ? number_format($booking->amount_to_pay, 2) : number_format($booking->relatedBookings->sum('amount_to_pay'), 2) }}
                        </p>
                        <p><strong>Amount Paid:</strong> ₱
                            {{ $booking->type != 'bulk_head_online' ? number_format($booking->amount_paid, 2) : number_format($booking->relatedBookings->sum('amount_paid'), 2) }}
                        </p>

                        @php
                            $chargesAmount = 0;
                            if ($booking->type != 'bulk_head_online') {
                                foreach ($booking->additional_charges ?? [] as $charge) {
                                    $chargesAmount += $charge['total_charges'];
                                }
                            } else {
                                foreach ($booking->relatedBookings as $value) {
                                    foreach ($value->additional_charges ?? [] as $charge) {
                                        $chargesAmount += $charge['total_charges'];
                                    }
                                }
                            }

                        @endphp

                        <p><strong>Balance:</strong> ₱ {{ number_format($booking->balance + $chargesAmount, 2) }}
                        </p>
                        <p><strong>Suite Type:</strong> {{ $booking->room->name ?? '-' }}</p>
                        <p><strong>Room:</strong>
                            @if ($booking->type != 'bulk_head_online')
                                {{ ucfirst($booking->suiteRoom->name ?? '-') }}
                            @else
                                @foreach ($booking->relatedBookings as $value)
                                    {{ ucfirst($value->suiteRoom->name ?? '-') }},
                                @endforeach
                            @endif
                        </p>
                    </div>
                </div>


                @if (!empty($booking->additional_charges))
                    <div style="margin-bottom: 30px;">
                        <h3 class="mb-30">Additional Charges</h3>
                        <ul class="features-list" style="list-style-type: none; padding: 0;">
                            @foreach ($booking->additional_charges as $charge)
                                <li>
                                    {{ $charges[$charge['name']] }} -
                                    ₱ {{ number_format($charge['amount'], 2) }} x {{ $charge['quantity'] }} =
                                    {{ $charge['total_charges'] }}
                            @endforeach
                    </div>
                @endif

                @if (!empty($booking->notes))
                    <hr>
                    <h4>Notes:</h4>
                    <p>{{ $booking->notes }}</p>
                @endif
                <div>

                    <div
                        class="flex flex-col items-center gap-4 p-8 bg-white rounded-lg shadow-lg border w-full max-w-xs text-center">

                        <h1 class="text-xl font-bold text-gray-800">
                            Scan to Pay with GCash
                        </h1>

                        <div class="text-center">

                            <h1 class="text-sm font-bold text-gray-800">
                                50% Deposit Required ₱
                                {{ $booking->type != 'bulk_head_online' ? number_format($booking->amount_to_pay / 2, 2) : number_format($booking->relatedBookings->sum('amount_to_pay') / 2, 2) }}
                            </h1>
                        </div>

                        <div>
                            <img src="{{ asset('images/qr.jpg') }}" alt="GCash QR Code" width="400" height="400"
                                class="rounded-md border-2 border-gray-200">
                        </div>
                    </div>

                    <div class="w-full mt-4">
                        {{ $this->form }}
                    </div>
                </div> --}}

                @php
                    // --- Data Calculation Block (Centralized for clean access) ---
                    $chargesAmount = 0;
                    $foodChargesAmount = 0;
                    // Determine the target bookings (either the single booking or the related bulk bookings)
                    $targetBookings =
                        $booking->type != 'bulk_head_online' ? collect([$booking]) : $booking->relatedBookings;

                    // Calculate total additional charges across all relevant bookings
                    foreach ($targetBookings as $b) {
                        foreach ($b->additional_charges ?? [] as $charge) {
                            $chargesAmount += $charge['total_charges'] ?? 0;
                        }
                    }

                    // Calculate total additional food charges across all relevant bookings
                    foreach ($targetBookings as $b) {
                        foreach ($b->food_charges ?? [] as $charge) {
                            $foodChargesAmount += $charge['total_charges'] ?? 0;
                        }
                    }

                    // Calculate financial totals
                    $totalAmount =
                        $booking->type != 'bulk_head_online'
                            ? $booking->amount_to_pay
                            : $booking->relatedBookings->sum('amount_to_pay');
                    $amountPaid =
                        $booking->type != 'bulk_head_online'
                            ? $booking->amount_paid
                            : $booking->relatedBookings->sum('amount_paid');
                    $balance = $totalAmount - $amountPaid + $chargesAmount + $foodChargesAmount;
                    $depositRequired = $totalAmount / 2;

                    // Status Badge Logic
                    $status = $booking->status;
                    $statusLabel = match ($status) {
                        'completed' => 'For Check In',
                        'done' => 'Settled',
                        'cancelled' => 'Cancelled',
                        'returned' => 'Returned',
                        'no show - call' => 'No Show (Call)',
                        default => ucfirst($status),
                    };
                    $statusColor = match ($status) {
                        'done', 'completed' => 'success',
                        'cancelled', 'returned', 'no show - call' => 'danger',
                        default => 'warning',
                    };
                @endphp

                <div class="space-y-6">

                    {{-- Header & Action Button --}}
                    <div class="flex items-start justify-between">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Booking Details - #{{ $booking->booking_number }}
                        </h1>

                        {{-- Filament Modal Button (RETAINED) --}}
                        <x-filament::modal id="mail-modal">
                            <x-slot name="trigger">
                                <x-filament::button color="success" class="mb-5">
                                    Mail
                                </x-filament::button>
                            </x-slot>

                            <Textarea label="Reason for cancellation" wire:model.defer="reason"></Textarea>

                            <x-slot name="footerActions">
                                <a href="#" class="genric-btn info w-full" wire:click="cancel">Confirm</a>
                                <a href="#" class="genric-btn danger w-full"
                                    x-on:click.prevent="$dispatch('close-modal', {id: 'mail-modal'})">Cancel</a>
                            </x-slot>
                        </x-filament::modal>
                    </div>

                    {{-- Main Information Section (Using Table for Structure) --}}
                    <x-filament::section collapsible>
                        <x-slot name="heading">
                            Guest, Room, & Reservation Details
                        </x-slot>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <tbody>
                                    {{-- Row 1: Guest Info & Status --}}
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-2 py-3 font-semibold text-gray-600 dark:text-gray-400 w-1/4">Name
                                            / Contact</td>
                                        <td class="px-2 py-3 text-gray-900 dark:text-white w-1/4">
                                            {{ $booking->user->name }} / {{ $booking->user->contact_number }}</td>
                                        <td class="px-2 py-3 font-semibold text-gray-600 dark:text-gray-400 w-1/4">
                                            Status</td>
                                        <td class="px-2 py-3 w-1/4">
                                            <span
                                                class="badge badge-{{ $booking->status == 'done' ? 'success' : ($booking->status == 'cancelled' ? 'danger' : ($booking->status == 'completed' ? 'warning' : 'secondary')) }}">
                                                {{ $booking->status == 'completed' ? 'For CheckIn' : ($booking->status == 'done' ? 'Settled' : ucfirst($booking->status)) }}
                                            </span>
                                        </td>
                                    </tr>

                                    {{-- Row 2: Dates & Duration --}}
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-2 py-3 font-semibold text-gray-600 dark:text-gray-400">Check-in /
                                            Check-out</td>
                                        <td class="px-2 py-3 text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($booking->check_in_date)->format('F j, Y h:i A') }}
                                            to
                                            {{ \Carbon\Carbon::parse($booking->check_out_date)->format('F j, Y h:i A') }}
                                        </td>
                                        <td class="px-2 py-3 font-semibold text-gray-600 dark:text-gray-400">Duration
                                        </td>
                                        <td class="px-2 py-3 text-gray-900 dark:text-white">{{ $booking->days }} Days /
                                            {{ $booking->duration }} Hrs</td>
                                    </tr>

                                    {{-- Row 3: Room Info & Guests --}}
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-2 py-3 font-semibold text-gray-600 dark:text-gray-400">Room Type /
                                            Room</td>
                                        <td class="px-2 py-3 text-gray-900 dark:text-white">
                                            {{ $booking->room->name ?? '-' }} /
                                            @if ($booking->type != 'bulk_head_online')
                                                <span
                                                    class="font-medium">{{ ucfirst($booking->suiteRoom->name ?? '-') }}</span>
                                            @else
                                                @foreach ($booking->relatedBookings as $value)
                                                    <span
                                                        class="font-medium">{{ ucfirst($value->suiteRoom->name ?? '-') }}</span>
                                                    @if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="px-2 py-3 font-semibold text-gray-600 dark:text-gray-400">Total
                                            Persons</td>
                                        <td class="px-2 py-3 text-gray-900 dark:text-white">
                                            {{ $booking->type != 'bulk_head_online' ? $booking->no_persons : $booking->relatedBookings->sum('no_persons') }}
                                            base
                                            (+
                                            {{ $booking->type != 'bulk_head_online' ? $booking->additional_persons : $booking->relatedBookings->sum('additional_persons') }}
                                            extra)
                                        </td>
                                    </tr>

                                    {{-- Row 4: Notes/Reason --}}
                                    @if ($booking->status == 'returned' || $booking->status == 'cancelled')
                                        <tr>
                                            <td class="px-2 py-3 font-semibold text-gray-600 dark:text-gray-400">
                                                Notes/Reason</td>
                                            <td class="px-2 py-3 text-red-500 italic" colspan="3">
                                                {{ $booking->return_notes ?? $booking->cancel_reason }}
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </x-filament::section>

                    {{-- Financial Summary and QR Code (Two Columns) --}}
                    <div class="md:grid md:grid-cols-3 gap-6">

                        {{-- Section: Financial Summary (RETAINED CLEAN STYLING) --}}
                        <x-filament::section class="col-span-2" collapsible>
                            <x-slot name="heading">
                                Financial Summary (₱)
                            </x-slot>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center border-b pb-1">
                                    <span class="font-medium text-gray-600">Total Booking Amount:</span>
                                    <span class="font-bold text-lg">₱ {{ number_format($totalAmount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Room Charges:</span>
                                    <span class="text-red-500 font-medium">₱
                                        {{ number_format($chargesAmount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Food Charges:</span>
                                    <span class="text-red-500 font-medium">₱
                                        {{ number_format($foodChargesAmount, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b pb-1">
                                    <span class="text-gray-600">Amount Paid:</span>
                                    <span class="text-green-600 font-medium">₱
                                        {{ number_format($amountPaid, 2) }}</span>
                                </div>
                                <div
                                    class="flex justify-between items-center pt-2 border-t-2 border-gray-300 dark:border-gray-700">
                                    <span class="text-lg font-bold">CURRENT BALANCE:</span>
                                    <span class="text-xl font-extrabold text-primary-600">₱
                                        {{ number_format($balance, 2) }}</span>
                                </div>
                            </div>
                        </x-filament::section>

                        {{-- Section: GCash QR Code --}}
                        <x-filament::section class="text-center mt-4" collapsible>
                            <x-slot name="heading">
                                Scan to Pay (GCash)
                            </x-slot>
                            <div class="flex flex-col items-center space-y-4">
                                <p class="text-sm font-bold text-gray-600 dark:text-gray-400">
                                    50% Deposit Required:
                                    <span class="text-lg text-primary-600 font-extrabold">₱
                                        {{ number_format($depositRequired, 2) }}</span>
                                </p>
                                <img src="{{ asset('images/qr.jpg') }}" alt="GCash QR Code"
                                    class="rounded-md border-2 border-gray-200 w-full max-w-xs mx-auto">
                            </div>
                        </x-filament::section>
                    </div>

                    {{-- Additional Charges Breakout --}}
                    @if (!empty($booking->additional_charges) || ($booking->type == 'bulk_head_online' && $chargesAmount > 0))
                        <x-filament::section class="mt-4" collapsible>
                            <x-slot name="heading">
                                Room Charges Breakdown
                            </x-slot>
                            <div class="space-y-4">
                                @foreach ($targetBookings as $b)
                                    {{-- Check if this specific booking has any charges --}}
                                    @if (!empty($b->additional_charges))
                                        {{-- Sub-heading for the current booking/room --}}
                                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                                            <table class="w-full text-sm text-gray-600 dark:text-gray-400">
                                                <thead class="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="px-3 py-2 text-left font-medium">Description</th>
                                                        <th class="px-3 py-2 text-center font-medium">Unit Price (₱)
                                                        </th>
                                                        <th class="px-3 py-2 text-center font-medium">Qty</th>
                                                        <th
                                                            class="px-3 py-2 text-right font-medium text-red-600 dark:text-red-400">
                                                            Total (₱)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($b->additional_charges as $charge)
                                                        <tr
                                                            class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                                            <td
                                                                class="px-3 py-2 font-medium text-gray-900 dark:text-white">
                                                                {{ $charges[$charge['name']] ?? 'Unknown Charge' }}
                                                            </td>
                                                            <td class="px-3 py-2 text-center">
                                                                {{ number_format($charge['amount'], 2) }}
                                                            </td>
                                                            <td class="px-3 py-2 text-center">
                                                                {{ $charge['quantity'] ?? 1 }}
                                                            </td>
                                                            <td
                                                                class="px-3 py-2 text-right font-semibold text-red-600 dark:text-red-400">
                                                                {{ number_format($charge['total_charges'] ?? 0, 2) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endforeach

                                {{-- Handle case where targetBookings is empty or has no charges --}}
                                @if ($targetBookings->isEmpty() || $targetBookings->every(fn($b) => empty($b->additional_charges)))
                                    <p class="text-gray-500 italic text-sm p-2">No room charges recorded for this
                                        booking.</p>
                                @endif

                            </div>
                        </x-filament::section>
                    @endif

                    @if (!empty($booking->food_charges) || ($booking->type == 'bulk_head_online' && $chargesAmount > 0))
                        <x-filament::section class="mt-4" collapsible>
                            <x-slot name="heading">
                                Food Charges Breakdown
                            </x-slot>
                            <div class="space-y-4">
                                @foreach ($targetBookings as $b)
                                    {{-- Check if this specific booking has any charges --}}
                                    @if (!empty($b->food_charges))
                                        {{-- Sub-heading for the current booking/room --}}
                                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                                            <table class="w-full text-sm text-gray-600 dark:text-gray-400">
                                                <thead class="bg-gray-50 dark:bg-gray-800">
                                                    <tr>
                                                        <th class="px-3 py-2 text-left font-medium">Description</th>
                                                        <th class="px-3 py-2 text-center font-medium">Unit Price (₱)
                                                        </th>
                                                        <th class="px-3 py-2 text-center font-medium">Qty</th>
                                                        <th
                                                            class="px-3 py-2 text-right font-medium text-red-600 dark:text-red-400">
                                                            Total (₱)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($b->food_charges as $charge)
                                                        <tr
                                                            class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900/50">
                                                            <td
                                                                class="px-3 py-2 font-medium text-gray-900 dark:text-white">
                                                                {{ $foodCharges[$charge['name']] ?? 'Unknown Charge' }}
                                                            </td>
                                                            <td class="px-3 py-2 text-center">
                                                                {{ number_format($charge['amount'], 2) }}
                                                            </td>
                                                            <td class="px-3 py-2 text-center">
                                                                {{ $charge['quantity'] ?? 1 }}
                                                            </td>
                                                            <td
                                                                class="px-3 py-2 text-right font-semibold text-red-600 dark:text-red-400">
                                                                {{ number_format($charge['total_charges'] ?? 0, 2) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endforeach

                                {{-- Handle case where targetBookings is empty or has no charges --}}
                                @if ($targetBookings->isEmpty() || $targetBookings->every(fn($b) => empty($b->food_charges)))
                                    <p class="text-gray-500 italic text-sm p-2">No food charges recorded for this
                                        booking.</p>
                                @endif

                            </div>
                        </x-filament::section>
                    @endif

                    {{-- Filament Form Area (RETAINED) --}}
                    {{ $this->form }}
                </div>


            </div>
        </div>
    </div>





    <div class="container" class="text-right">
        @if ($booking->status == 'pending')

            @if ($booking->is_proof_send)
                <a href="#" class="genric-btn info mb-4" style="margin-top: 190px;"> Waiting for
                    Confirmation...</a>
            @else
                <x-filament::modal id="confirm-modal" width="md" alignment="center" icon="heroicon-o-check"
                    icon-color="info">
                    <x-slot name="trigger">
                        <a href="#" class="genric-btn info mb-5">Confirm Payment</a>
                    </x-slot>
                    <x-slot name="heading">
                        Confirm Payment
                    </x-slot>

                    <x-slot name="description">
                        Are you sure you would like to do this?
                    </x-slot>

                    <x-slot name="footerActions">
                        <a href="#" class="genric-btn info w-full" wire:click="pay">Confirm</a>
                        <a href="#" class="genric-btn danger w-full"
                            x-on:click.prevent="$dispatch('close-modal', {id: 'confirm-modal'})">Cancel</a>
                    </x-slot>
                </x-filament::modal>
            @endif
        @else
            @if ($booking->status == 'returned')
                <x-filament::modal id="confirm-modal" width="md" alignment="center" icon="heroicon-o-check"
                    icon-color="info">
                    <x-slot name="trigger">
                        <a href="#" class="genric-btn info mb-5">Confirm Payment</a>
                    </x-slot>
                    <x-slot name="heading">
                        Confirm Payment
                    </x-slot>

                    <x-slot name="description">
                        Are you sure you would like to do this?
                    </x-slot>

                    <x-slot name="footerActions">
                        <a href="#" class="genric-btn info w-full" wire:click="pay">Confirm</a>
                        <a href="#" class="genric-btn danger w-full"
                            x-on:click.prevent="$dispatch('close-modal', {id: 'confirm-modal'})">Cancel</a>
                    </x-slot>
                </x-filament::modal>
            @else
                <a href="#" class="genric-btn info mb-4" style="margin-top: 190px;"> Completed</a>
            @endif

        @endif
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    window.addEventListener('swal:success', event => {
        const {
            title,
            text,
            icon
        } = event.detail[0];

        Swal.fire({
            title: title ?? 'Success!',
            text: text ?? '',
            icon: icon ?? 'success',
            toast: true,
            position: 'top-end',
            timer: 10000,
            showConfirmButton: false,
        });
    });

    window.addEventListener('close-modal', event => {
        const modalId = event.detail.id;
        window.dispatchEvent(new CustomEvent('close-modal', {
            detail: {
                id: modalId
            }
        }));
    });
</script>
