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
                                        <li><a class="" href="{{ route('index') }}" style="color: black"><i
                                                    class="fa fa-home" style="font-size: 1.5em;"></i> home</a></li>
                                        @auth
                                            <li><a class="active" href="{{ route('my-bookings') }}" style="color: black"><i
                                                        class="fa fa-calendar" style="font-size: 1.5em;"></i> my
                                                    bookings</a></li>
                                        @endauth
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

                        <div class="flex gap-2">
                            {{-- Rebook Modal Button --}}
                            @if ($this->canRebook() || $booking->is_proof_send)
                                <x-filament::modal id="rebook-modal">
                                    <x-slot name="trigger">
                                        <x-filament::button color="primary" class="mb-5">
                                            Rebook
                                        </x-filament::button>
                                    </x-slot>

                                    <div class="space-y-4">
                                        <div>
                                            <label for="rebook_check_in_date"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Check In Date
                                            </label>
                                            <input type="datetime-local" id="rebook_check_in_date"
                                                wire:model="rebook_check_in_date"
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                required>
                                            @error('rebook_check_in_date')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="rebook_check_out_date"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Check Out Date
                                            </label>
                                            <input type="datetime-local" id="rebook_check_out_date"
                                                wire:model="rebook_check_out_date"
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                required>
                                            @error('rebook_check_out_date')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="rebook_notes"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Notes/Requests
                                            </label>
                                            <textarea id="rebook_notes" wire:model="rebook_notes" rows="4"
                                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                                placeholder="Please provide any notes or requests" required maxlength="255"></textarea>
                                            @error('rebook_notes')
                                                <span class="text-red-500 text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <x-slot name="footerActions">
                                        <a href="#" class="genric-btn info w-full" wire:click="rebook">Confirm</a>
                                        <a href="#" class="genric-btn danger w-full"
                                            x-on:click.prevent="$dispatch('close-modal', {id: 'rebook-modal'})">Cancel</a>
                                    </x-slot>
                                </x-filament::modal>
                            @endif

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
                                        <td class="px-2 py-3 text-gray-900 dark:text-white">{{ $booking->days }} Days
                                            /
                                            {{ $booking->duration }} Hrs</td>
                                    </tr>

                                    {{-- Row 3: Room Info & Guests --}}
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-2 py-3 font-semibold text-gray-600 dark:text-gray-400">Room Type
                                            /
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
                                        <td class="px-2 py-3 font-semibold text-gray-600 dark:text-gray-400">
                                            Total Persons</td>
                                        <td class="px-2 py-3 text-gray-900 dark:text-white">
                                            {{ $booking->type != 'bulk_head_online' ? $booking->no_persons : $booking->relatedBookings->sum('no_persons') }}
                                            base
                                            (+
                                            {{ $booking->type != 'bulk_head_online' ? $booking->additional_persons + $booking->additional_child : $booking->relatedBookings->sum('additional_persons') + $booking->relatedBookings->sum('additional_child') }}
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
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Adult Charges:</span>
                                    <span class="text-red-500 font-medium">₱
                                        {{ number_format($booking->adult_payment, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Child Charges:</span>
                                    <span class="text-red-500 font-medium">₱
                                        {{ number_format($booking->child_payment, 2) }}</span>
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
