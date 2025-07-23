<div x-data="{ open: false }">

    @if ($activePage == 'home')
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
                                            <li><a class="active" href="{{ route('index') }}">home</a></li>

                                            @auth
                                                <li><a class="" href="{{ route('my-bookings') }}">my bookings</a></li>
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
                                    @auth
                                        <button @click="open = true" class="btn btn-outline-secondary position-relative ">
                                            <i class="fa fa-bell fs-5"></i>
                                            @if ($notifications->count())
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    {{ $notifications->count() }}
                                                </span>
                                            @endif
                                        </button>
                                    @endauth

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

        @auth
            <!-- Overlay -->
            <div x-show="open" x-transition.opacity @click="open = false"
                class="position-fixed top-0 start-0 w-100 h-200 bg-opacity-50" x-cloak>
            </div>

            <!-- Drawer -->
            <div x-show="open" x-transition:enter="transition transform duration-300"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition transform duration-300" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="-translate-x-full"
                class="position-fixed top-0 start-0 bg-white h-100 shadow-lg border-end rounded-end"
                style="width: 340px; z-index: 1050" x-cloak>
                <!-- Header -->
                <div class="px-4 py-3 border-bottom bg-light">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Notifications</h5>
                        <button class="btn btn-sm btn-light" @click="open = false" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mt-3">
                        <button wire:click="markAllAsRead" class="btn btn-sm btn-outline-success" title="Mark all as read">
                            <i class="fa fa-check me-1"></i> Read All
                        </button>

                        <button wire:click="clearAll" class="btn btn-sm btn-outline-danger" wire:click="clearAll"
                            title="Clear all notifications">
                            <i class="fa fa-trash me-1"></i> Clear All
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-4 overflow-auto" style="max-height: calc(100vh - 65px);">
                    @forelse($notifications as $notification)
                        <div class="d-flex align-items-start mb-3 p-3 bg-light rounded shadow-sm notification-item"
                            style="transition: background-color 0.2s;">
                            <div class="me-3">
                                <i class="fa fa-bell text-primary fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-dark">
                                    {{ $notification->data['title'] ?? 'Notification' }}
                                </div>
                                <div class="small text-muted">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted mt-4">
                            <i class="fa fa-check-circle fa-2x mb-2"></i>
                            <p>No new notifications.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endauth


        <!-- slider_area_start -->
        <div class="slider_area">
            <div class="slider_active owl-carousel">
                <div class="single_slider d-flex align-items-center justify-content-center slider_bg_1">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="slider_text text-center">
                                    <h3>Millenium Suites</h3>
                                    <p>Unlock comfort. Enjoy. Relax.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single_slider  d-flex align-items-center justify-content-center slider_bg_2">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="slider_text text-center">
                                    <h3>Where comfort unlocks relaxation.</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="single_slider d-flex align-items-center justify-content-center slider_bg_3">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="slider_text text-center">
                                    <h3>Stay. Unwind. Repeat.</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- slider_area_end -->
        <!-- about_area_start -->
        <div class="about_area">
            <div class="container">
                <div class="row">
                    <div class="col-xl-5 col-lg-5">
                        <div class="about_info">
                            <div class="section_title mb-20px">
                                <span>About Us</span>
                                <h3>A Luxuries Hotel <br>
                                    with Nature</h3>
                            </div>
                            <p>A place for staycation and relaxation with best service and atmosphere for a price you
                                can
                                afford.</p>
                            <a href="#" class="line-button">Learn More</a>
                        </div>
                    </div>
                    <div class="col-xl-7 col-lg-7">
                        <div class="about_thumb d-flex">
                            <div class="img_1">
                                <img src="img/about/about1.PNG" alt="">
                            </div>
                            <div class="img_2">
                                <img src="img/about/about2.PNG" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- about_area_end -->

        <!-- about_area_start -->
        <div class="about_area">
            <div class="container">
                <div class="row">
                    <div class="col-xl-7 col-lg-7">
                        <div class="about_thumb2 d-flex">
                            <div class="img_1">
                                <img src="img/food/fries.jpg" alt="">
                            </div>
                            <div class="img_2">
                                <img src="img/food/chicken.jpg" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 col-lg-5">
                        <div class="about_info">
                            <div class="section_title mb-20px">
                                <span>Delicious Food</span>
                                <h3>We Serve Fresh and <br>
                                    Delicious Food</h3>
                            </div>
                            <p>Bringing You Freshly Prepared, Mouthwatering Dishes Crafted with Passion and the Finest
                                Ingredients.</p>
                            <a href="#" class="line-button">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- about_area_end -->

        <!-- features_room_startt -->
        <div class="features_room">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="section_title text-center mb-100">
                            <span>Featured Rooms</span>
                            <h3>Choose a Better Room</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rooms_here">
                <div class="single_rooms">
                    <div class="room_thumb">
                        <img src="{{ asset('suite-photo/' . $record['standard']['image']) }}" alt="">
                        <div class="room_heading d-flex justify-content-between align-items-center">
                            <div class="room_heading_inner">
                                <span>From ₱ 1300/night</span>
                                <h3>Standard Suite</h3>
                            </div>
                            <a href="#" class="line-button" wire:click.prevent="viewRoom('1')">View</a>
                        </div>
                    </div>
                </div>
                <div class="single_rooms">
                    <div class="room_thumb">
                        <img src="{{ asset('suite-photo/' . $record['deluxe']['image']) }}" alt="">
                        <div class="room_heading d-flex justify-content-between align-items-center">
                            <div class="room_heading_inner">
                                <span>From ₱ 1500/night</span>
                                <h3>Deluxe Suite</h3>
                            </div>
                            <a href="#" class="line-button" wire:click.prevent="viewRoom('2')">View</a>
                        </div>
                    </div>
                </div>
                <div class="single_rooms">
                    <div class="room_thumb">
                        <img src="{{ asset('suite-photo/' . $record['executive']['image']) }}" alt=""
                            style="height: 600px;">
                        <div class="room_heading d-flex justify-content-between align-items-center">
                            <div class="room_heading_inner">
                                <span>From ₱ 1700/night</span>
                                <h3>Executive Suite</h3>
                            </div>
                            <a href="#" class="line-button" wire:click.prevent="viewRoom('3')">View</a>
                        </div>
                    </div>
                </div>
                <div class="single_rooms">
                    <div class="room_thumb">
                        <img src="{{ asset('suite-photo/' . $record['functionHall']['image']) }}" alt=""
                            style="height: 600px;">
                        <div class="room_heading d-flex justify-content-between align-items-center">
                            <div class="room_heading_inner">
                                {{-- <span>From $250/night</span> --}}
                                <h3>Function Hall</h3>
                            </div>
                            <a href="#" class="line-button" wire:click.prevent="viewRoom('4')">View</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- features_room_end -->
    @endif

    @if ($activePage == 'viewRoom')
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
                                            <li><a class="active" href="{{ route('index') }}">home</a></li>
                                            @auth
                                                <li><a class="" href="{{ route('my-bookings') }}">my bookings</a>
                                                </li>
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
            style="background-image: url('{{ asset('suite-photo/' . $selectedRoom['image']) }}'); background-size: cover; background-position: center; height: 100%;">
            <h3>{{ $selectedRoom['name'] }}</h3>
        </div>

        <!-- bradcam_area_end -->

        <!-- Start Sample Area -->

        <section class="sample-text-area">
            <div class="container box_1170">
                <h3 class="text-heading">About</h3>
                <p class="sample-text">
                    @if ($selectedRoom['name'] == 'Standard Suite')
                        "Our Standard Suite offers a perfect blend of comfort and elegance — featuring a cozy bed,
                        modern amenities, and a calming atmosphere ideal for both rest and relaxation. Enjoy quality
                        service in a space designed to feel like home."
                    @endif

                    @if ($selectedRoom['name'] == 'Deluxe Suite')
                        "Our Deluxe Suite offers a luxurious experience with a spacious layout, top-of-the-line
                        amenities, and a comfortable atmosphere that will make you feel at home. Whether you're
                        looking
                        for a cozy bed, a comfortable couch, or a stylish sofa, our Deluxe Suite has got you
                        covered."
                    @endif

                    @if ($selectedRoom['name'] == 'Executive Suite')
                        "Our Executive Suite is the perfect choice for those who want a spacious and elegant space
                        with top-of-the-line amenities. With a modern design and a comfortable atmosphere, our
                        Executive Suite is the perfect place to relax and unwind."
                    @endif

                    @if ($selectedRoom['name'] == 'Function Hall')
                        "Our Function Hall is the perfect place to relax and unwind. With a comfortable bed, a
                        stylish sofa, and a cozy couch, our Function Hall is the perfect space to unwind and enjoy
                        some much-needed rest."
                    @endif
                </p>

                @if ($selectedRoom['name'] != 'Function Hall')
                    <div class="row">
                        <!-- Rates Column -->
                        <div class="col-md-6 mb-4">
                            <h3 class="mb-20">Rates</h3>
                            @if ($selectedRoom['name'] == 'Standard Suite')
                                @foreach ($record['standard']['items'] ?? [] as $item)
                                    <ul class="features-list"
                                        style="list-style-type: none; padding: 0; font-size: 15px;">
                                        <li>₱ {{ $item['price'] }} - {{ $item['item'] }}</li>
                                    </ul>
                                @endforeach
                            @endif

                            @if ($selectedRoom['name'] == 'Deluxe Suite')
                                @foreach ($record['deluxe']['items'] ?? [] as $item)
                                    <ul class="features-list"
                                        style="list-style-type: none; padding: 0; font-size: 15px;">
                                        <li>₱ {{ $item['price'] }} - {{ $item['item'] }}</li>
                                    </ul>
                                @endforeach
                            @endif

                            @if ($selectedRoom['name'] == 'Executive Suite')
                                @foreach ($record['executive']['items'] ?? [] as $item)
                                    <ul class="features-list"
                                        style="list-style-type: none; padding: 0; font-size: 15px;">
                                        <li>₱ {{ $item['price'] }} - {{ $item['item'] }}</li>
                                    </ul>
                                @endforeach
                            @endif
                        </div>

                        <!-- Amenities Column -->
                        <div class="col-md-6 mb-4">
                            <h3 class="mb-20">Amenities</h3>
                            <ul class="unordered-list" style="font-size: 14px;">
                                <li>Airconditioned Room</li>
                                <li>Essential Kit</li>
                                <li>Complimentary Bottled Water</li>
                                <li>Parking space</li>
                                <li>Fire Alarm</li>
                                <li>Good for 2 pax</li>
                            </ul>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <!-- Rates Column -->
                        <div class="col-md-6 mb-4">
                            <h3 class="mb-20">Rates</h3>
                            @foreach ($record['functionHall']['items'] ?? [] as $item)
                                <ul class="features-list" style="list-style-type: none; padding: 0; font-size: 15px;">
                                    <li>₱ {{ $item['price'] }} - {{ $item['item'] }}</li>
                                </ul>
                            @endforeach
                        </div>

                        <!-- Amenities Column -->
                        <div class="col-md-6 mb-4">
                            <h3 class="mb-20">Amenities</h3>
                            <ul class="unordered-list" style="font-size: 14px;">
                                <li>4 hours Rental</li>
                                <li>Airconditioned Room</li>
                                <li>Basic Sound System</li>
                                <li>Standby Generator</li>
                                <li>Good for 30 pax</li>
                            </ul>
                        </div>
                    </div>

                @endif
            </div>
        </section>

        <div class="whole-wrap">
            <div class="container box_1170">
                <div class="section-top-border">
                    @if ($this->selectedRoom['name'] === 'Function Hall')
                        <h3>Image Gallery</h3>
                        {{-- <div class="row gallery-item">
                            <div class="col-md-4">
                                <a href="img/elements/g1.jpg" class="img-pop-up">
                                    <div class="single-gallery-image" style="background: url(img/elements/g1.jpg);">
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="img/elements/g2.jpg" class="img-pop-up">
                                    <div class="single-gallery-image" style="background: url(img/elements/g2.jpg);">
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="img/elements/g3.jpg" class="img-pop-up">
                                    <div class="single-gallery-image" style="background: url(img/elements/g3.jpg);">
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="img/elements/g4.jpg" class="img-pop-up">
                                    <div class="single-gallery-image" style="background: url(img/elements/g4.jpg);">
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="img/elements/g5.jpg" class="img-pop-up">
                                    <div class="single-gallery-image" style="background: url(img/elements/g5.jpg);">
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="img/elements/g6.jpg" class="img-pop-up">
                                    <div class="single-gallery-image" style="background: url(img/elements/g6.jpg);">
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="img/elements/g7.jpg" class="img-pop-up">
                                    <div class="single-gallery-image" style="background: url(img/elements/g7.jpg);">
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="img/elements/g8.jpg" class="img-pop-up">
                                    <div class="single-gallery-image" style="background: url(img/elements/g8.jpg);">
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                        <div class="instragram_area">
                            <div class="single_instagram">
                                <img src="img/functionhall/fn1.jpg" alt="">
                            </div>
                            <div class="single_instagram">
                                <img src="img/functionhall/fn2.jpg" alt="">
                            </div>
                            <div class="single_instagram">
                                <img src="img/functionhall/fn3.jpg" alt="">
                            </div>
                            <div class="single_instagram">
                                <img src="img/functionhall/fn4.jpg" alt="">
                            </div>
                            <div class="single_instagram">
                                <img src="img/functionhall/fn5.jpg" alt="">
                            </div>
                        </div>
                </div>
            @else
                {{-- @switch($this->selectedRoom['name'])
                    @case('Standard Suite')
                        <div class="text-right"> {{ 7 - $record['standardOccupied'] }} Available Room(s)</div>
                        {{ $this->standardSuiteForm }}
                    @break

                    @case('Deluxe Suite')
                        <div class="text-right"> {{ 7 - $record['deluxeOccupied'] }} Available Room(s)</div>
                        {{ $this->deluxeSuiteForm }}
                    @break

                    @case('Executive Suite')
                        <div class="text-right"> {{ 13 - $record['executiveOccupied'] }} Available Room(s)</div>
                        {{ $this->executiveSuiteForm }}
                    @break
                @endswitch

                <div class="text-right mt-4">
                    <div class="book_btn d-none d-lg-block">
                        <a href="#" class="genric-btn info" wire:click.prevent="bookRoom">
                            Book Now
                        </a>
                    </div>
                </div> --}}
                @php
                    $available = 0;
                @endphp

                @switch($this->selectedRoom['name'])
                    @case('Standard Suite')
                        @php $available = 7 - $record['standardOccupied']; @endphp
                        <div class="text-right"> {{ $available }} Available Room(s) Today</div>
                        {{ $this->standardSuiteForm }}
                    @break

                    @case('Deluxe Suite')
                        @php $available = 7 - $record['deluxeOccupied']; @endphp
                        <div class="text-right"> {{ $available }} Available Room(s) Today</div>
                        {{ $this->deluxeSuiteForm }}
                    @break

                    @case('Executive Suite')
                        @php $available = 13 - $record['executiveOccupied']; @endphp
                        <div class="text-right"> {{ $available }} Available Room(s) Today</div>
                        {{ $this->executiveSuiteForm }}
                    @break
                @endswitch

                @if ($available > 0)
                    <div class="text-right mt-4">
                        <div class="book_btn d-none d-lg-block">
                            <a href="#" class="genric-btn info" wire:click.prevent="bookRoom">
                                Book Now
                            </a>
                        </div>
                    </div>
                @endif
    @endif
</div>
</div>
</div>

<!-- End Sample Area -->
@endif

<!-- footer -->
<footer class="footer">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <h3 class="footer_title">
                            address
                        </h3>
                        <p class="footer_text"> J.P. Laurel, Panabo City, Davao del Norte</p>
                        <a href="https://www.google.com/maps/dir/7.024234,125.49119/7.284512,125.6712799/@7.1522591,125.4171992,11z/data=!3m1!4b1!4m4!4m3!1m1!4e1!1m0?entry=ttu&g_ep=EgoyMDI1MDcxNi4wIKXMDSoASAFQAw%3D%3D"
                            class="line-button">Get Direction</a>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <h3 class="footer_title">
                            Reservation
                        </h3>
                        <p class="footer_text">0936 461 2236<br>
                            msuites.dzd@gmail.com
                        </p>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-lg-3">
                    <div class="footer_widget">
                        <h3 class="footer_title">
                            <a href="{{ route('policy') }}">Policy</a>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
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
            timer: 3000,
            showConfirmButton: false,
        });
    });
</script>
