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
                <h3 class="mb-30">Booking Information</h3>

                <div class="row">
                    <div class="col-md-6">
                        {{-- Booking Info Left --}}
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
                    </div>

                    <div class="col-md-6">
                        {{-- Booking Info Right --}}
                        <p><strong>Number of Persons:</strong> {{ $booking->no_persons }}</p>
                        <p><strong>Check In Time:</strong>
                            {{ \Carbon\Carbon::parse($booking->check_in_date)->format('F j, Y h:i A') }}</p>
                        <p><strong>Check Out Time:</strong>
                            {{ \Carbon\Carbon::parse($booking->check_out_date)->format('F j, Y h:i A') }}</p>
                        <p><strong>Amount:</strong> ₱ {{ number_format($booking->amount_to_pay, 2) }}</p>
                        <p><strong>Amount Paid:</strong> ₱ {{ number_format($booking->amount_paid, 2) }}</p>

                        @php
                            $chargesAmount = 0;
                            foreach ($booking->additional_charges ?? [] as $charge) {
                                $chargesAmount += $charge['amount'];
                            }
                        @endphp

                        <p><strong>Balance:</strong> ₱ {{ number_format($booking->balance + $chargesAmount, 2) }}</p>
                        <p><strong>Suite Type:</strong> {{ $booking->room->name ?? '-' }}</p>
                        <p><strong>Room:</strong> {{ ucfirst($booking->suiteRoom->name ?? '-') }}</p>
                    </div>
                </div>

                @if (!empty($booking->additional_charges))
                    <div style="margin-bottom: 30px;">
                        <h3 class="mb-30">Additional Charges</h3>
                        <ul class="features-list" style="list-style-type: none; padding: 0;">
                            @foreach ($booking->additional_charges as $charge)
                                <li>₱ {{ number_format($charge['amount'], 2) }} - {{ $charge['name'] }}</li>
                            @endforeach
                    </div>
                @endif

                @if (!empty($booking->notes))
                    <hr>
                    <h4>Notes:</h4>
                    <p>{{ $booking->notes }}</p>
                @endif

                {{-- Payment Form --}}
                <div>
                    Scan to Pay
                    <div>
                        <img src="{{ asset('images/qrcode.png') }}" alt="Image 1" width="200" height="200">
                    </div>
                    {{ $this->form }}
                </div>

                {{-- Book Button --}}
            </div>
        </div>
    </div>

    <div class="container" style="margin-top:-20px;" class="text-right">
        {{-- <div class="text-right" style="margin-bottom: 20px;">
            <a href="#" class="genric-btn info" wire:click.prevent="bookRoom">
                Book Now
            </a>
        </div> --}}
        @if ($booking->status == 'pending')

            @if ($booking->is_proof_send)
                <a href="#" class="genric-btn info mb-4" style="margin-top: 190px;"> Waiting for
                    Confirmation...</a>
            @else
                <x-filament::modal id="confirm-modal" width="md" alignment="center" icon="heroicon-o-check"
                    icon-color="info">
                    <x-slot name="trigger">
                        {{-- <x-filament::button color="info" class="mb-5">
                            Confirm Payment
                        </x-filament::button> --}}
                        <a href="#" class="genric-btn info mb-5">Confirm Payment</a>
                    </x-slot>
                    <x-slot name="heading">
                        Confirm Payment
                    </x-slot>

                    <x-slot name="description">
                        Are you sure you would like to do this?
                    </x-slot>

                    <x-slot name="footerActions">
                        {{-- <x-filament::button size="md" color="info" class="w-full" wire:click="pay">
                            Confirm
                        </x-filament::button> --}}
                        {{-- <x-filament::button color="gray" outlined size="md" class="w-full"
                            x-on:click.prevent="$dispatch('close-modal', {id: 'confirm-modal'})">
                            Cancel
                        </x-filament::button> --}}
                        <a href="#" class="genric-btn info w-full" wire:click="pay">Confirm</a>
                        <a href="#" class="genric-btn danger w-full"
                            x-on:click.prevent="$dispatch('close-modal', {id: 'confirm-modal'})">Cancel</a>
                    </x-slot>
                </x-filament::modal>
            @endif
        @else
            <a href="#" class="genric-btn info mb-4" style="margin-top: 190px;"> Completed</a>
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
        } = event.detail;

        Swal.fire({
            title: title ?? 'Success!',
            text: text ?? '',
            icon: icon ?? 'success',
            toast: true,
            position: 'top-end',
            timer: 3000,
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
