<div x-data="{ open: false }">

    @if ($activePage == 'home')
        <!-- header-start -->
        {{-- <header>
            <div class="header-area ">
                <div id="sticky-header" class="main-header-area">
                    <div class="container-fluid p-0">
                        <div class="row align-items-center no-gutters">
                            <div class="col-xl-5 col-lg-6">
                                <div class="main-menu  d-none d-lg-block">
                                    <nav>
                                        <ul id="navigation">
                                            <li><a class="active" href="{{ route('index') }}"><i class="fa fa-home" style="font-size: 1.5em;"></i> home</a></li>

                                            @auth
                                                <li><a class="" href="{{ route('my-bookings') }}"><i class="fa fa-calendar" style="font-size: 1.5em;"></i> my bookings</a></li>
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
                                            @if ($unreadNotificationsCount)
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    {{ $unreadNotificationsCount }}
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
        </header> --}}
        <header>
            <div class="header-area ">
                <div id="sticky-header" class="main-header-area">
                    <div class="container-fluid p-0">
                        <div class="row align-items-center no-gutters">
                            {{-- 
                        MAIN NAVIGATION (Hidden on mobile, duplicated into .mobile_menu)
                        The fix is inside this section.
                    --}}
                            <div class="col-xl-5 col-lg-6">
                                <div class="main-menu d-none d-lg-block">
                                    <nav>
                                        <ul id="navigation">
                                            <li><a class="active" href="{{ route('index') }}"><i class="fa fa-home" style="font-size: 1.5em;"></i> home</a></li>

                                            @auth
                                                <li><a class="" href="{{ route('my-bookings') }}"><i class="fa fa-calendar" style="font-size: 1.5em;"></i> my bookings</a></li>
                                            @endauth

                                            {{-- 
                                        ✨ FIX FOR MOBILE VIEW: 
                                        This <li> is only displayed on small screens (d-block d-lg-none) 
                                        and will be picked up by the mobile menu script. 
                                    --}}
                                            <li class="d-block d-lg-none">
                                                @auth
                                                    <a wire:click.prevent="logout">Logout</a>
                                                @endauth
                                                @guest
                                                    <a href="login">Login</a>
                                                @endguest
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>

                            {{-- Logo --}}
                            <div class="col-xl-2 col-lg-2">
                                <div class="logo-img">
                                    <a href="{{ route('index') }}">
                                        <img src="img/logo.png" alt="">
                                    </a>
                                </div>
                            </div>

                            {{-- BOOK ROOM / SOCIAL LINKS / NOTIFICATION (Desktop View) --}}
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

                                    {{-- Notification Bell (Desktop View) --}}
                                    @auth
                                        <button @click="open = true" class="btn btn-outline-secondary position-relative ">
                                            <i class="fa fa-bell fs-5"></i>
                                            @if ($unreadNotificationsCount)
                                                <span
                                                    class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                                    {{ $unreadNotificationsCount }}
                                                </span>
                                            @endif
                                        </button>
                                    @endauth

                                    {{-- Login/Logout Buttons (Desktop View) --}}
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

                            {{-- Mobile Menu Trigger/Container (The content is dynamically populated by JS) --}}
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
                        @php
                            $url = $notification->data['actions'][0]['url'] ?? '';

                            $parentUrl = dirname($url);
                            $bookingId = basename($parentUrl);
                        @endphp

                        <a href="{{ route('view-booking', $bookingId) }}" class="text-decoration-none text-dark d-block">
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
                        </a>
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

        @guest
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
        @endguest

        <!-- features_room_startt -->
        <div class="features_room" style="margin-top: 65px;">
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
            @php
                $items1 = $record['standard']['items'];
                $overnight1 = collect($items1)->firstWhere('item', 'Overnight Stay');
            @endphp
            <div class="rooms_here">
                <div class="single_rooms">
                    <div class="room_thumb">
                        <img src="{{ asset('suite-photo/' . $record['standard']['image']) }}" alt="">
                        <div class="room_heading d-flex justify-content-between align-items-center">
                            <div class="room_heading_inner">
                                <span>From ₱ {{ $overnight1['price'] }}/night</span>
                                <h3>Standard Suite</h3>
                            </div>
                            <a href="#" class="line-button" wire:click.prevent="viewRoom('1')">View</a>
                        </div>
                    </div>
                </div>
                @php
                    $items2 = $record['deluxe']['items'];
                    $overnight2 = collect($items2)->firstWhere('item', 'Overnight Stay');
                @endphp
                <div class="single_rooms">
                    <div class="room_thumb">
                        <img src="{{ asset('suite-photo/' . $record['deluxe']['image']) }}" alt="">
                        <div class="room_heading d-flex justify-content-between align-items-center">
                            <div class="room_heading_inner">
                                <span>From ₱ {{ $overnight2['price'] }}/night</span>
                                <h3>Deluxe Suite</h3>
                            </div>
                            <a href="#" class="line-button" wire:click.prevent="viewRoom('2')">View</a>
                        </div>
                    </div>
                </div>
                @php
                    $items3 = $record['executive']['items'];
                    $overnight3 = collect($items3)->firstWhere('item', 'Overnight Stay');
                @endphp
                <div class="single_rooms">
                    <div class="room_thumb">
                        <img src="{{ asset('suite-photo/' . $record['executive']['image']) }}" alt=""
                            style="height: 600px;">
                        <div class="room_heading d-flex justify-content-between align-items-center">
                            <div class="room_heading_inner">
                                <span>From ₱ {{ $overnight3['price'] }}/night</span>
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
                                            <li><a class="active" href="{{ route('index') }}"><i class="fa fa-home" style="font-size: 1.5em;"></i> home</a></li>
                                            @auth
                                                <li><a class="" href="{{ route('my-bookings') }}"><i class="fa fa-calendar" style="font-size: 1.5em;"></i> my bookings</a>
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

        <div x-data="{ activeTab: 'images' }" class="container mx-auto p-4 sm:p-6 lg:p-8">
            <!-- Tab navigation -->
            <div class="border-b border-gray-200 mb-8">
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    <button @click="activeTab = 'images'" :class="{ 'active-tab': activeTab === 'images' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-blue-600 hover:border-blue-300 transition-colors duration-200">
                        Images
                    </button>
                    <button @click="activeTab = 'about'" :class="{ 'active-tab': activeTab === 'about' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-blue-600 hover:border-blue-300 transition-colors duration-200">
                        About Us
                    </button>
                </nav>
            </div>

            <!-- Tab content panels -->
            <!-- Images Tab -->
            <div x-show="activeTab === 'images'" x-cloak x-transition>
                <div>
                    <h3 class="text-heading mt-3">Images</h3>
                    <div class="row gallery-item">
                        @if ($selectedRoom['name'] == 'Function Hall')
                            <div class="functionhall-carousel owl-carousel p-4">
                                <div class="item">
                                    <img src="img/functionhall/fn1.jpg" alt="Function Hall Image 1" style="width: 100%; height: auto;">
                                </div>
                                <div class="item">
                                    <img src="img/functionhall/fn2.jpg" alt="Function Hall Image 2" style="width: 100%; height: auto;">
                                </div>
                                <div class="item">
                                    <img src="img/functionhall/fn3.jpg" alt="Function Hall Image 3" style="width: 100%; height: auto;">
                                </div>
                                <div class="item">
                                    <img src="img/functionhall/fn4.jpg" alt="Function Hall Image 4" style="width: 100%; height: auto;">
                                </div>
                                <div class="item">
                                    <img src="img/functionhall/fn5.jpg" alt="Function Hall Image 5" style="width: 100%; height: auto;">
                                </div>
                            </div>
                        @else
                            <div class="suite-images-carousel owl-carousel p-4">
                                @foreach ($selectedRoom['images'] ?? [] as $image)
                                    <div class="item">
                                        <img src="{{ asset('suite-photo/' . $image) }}" alt="Suite Image" style="width: 100%; height: auto;">
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>

                </div>
            </div>

            <!-- About Us Tab -->
            <div x-show="activeTab === 'about'" x-cloak x-transition>
                <div>
                    <h3 class="text-heading mt-3">About</h3>
                    <p class="sample-text">
                        @if ($selectedRoom['name'] == 'Standard Suite')
                            "Our Standard Suite offers a perfect blend of comfort and elegance — featuring a
                            cozy bed,
                            modern amenities, and a calming atmosphere ideal for both rest and relaxation.
                            Enjoy
                            quality
                            service in a space designed to feel like home."
                        @endif

                        @if ($selectedRoom['name'] == 'Deluxe Suite')
                            "Our Deluxe Suite offers a luxurious experience with a spacious layout,
                            top-of-the-line
                            amenities, and a comfortable atmosphere that will make you feel at home. Whether
                            you're
                            looking
                            for a cozy bed, a comfortable couch, or a stylish sofa, our Deluxe Suite has got
                            you
                            covered."
                        @endif

                        @if ($selectedRoom['name'] == 'Executive Suite')
                            "Our Executive Suite is the perfect choice for those who want a spacious and
                            elegant
                            space
                            with top-of-the-line amenities. With a modern design and a comfortable
                            atmosphere,
                            our
                            Executive Suite is the perfect place to relax and unwind."
                        @endif

                        @if ($selectedRoom['name'] == 'Function Hall')
                            "Our Function Hall is the perfect place to relax and unwind. With a comfortable
                            bed,
                            a
                            stylish sofa, and a cozy couch, our Function Hall is the perfect space to unwind
                            and
                            enjoy
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
                                    <ul class="features-list"
                                        style="list-style-type: none; padding: 0; font-size: 15px;">
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
                                    <li>Good for 40 pax</li>
                                </ul>
                            </div>
                        </div>

                    @endif

                </div>
            </div>
        </div>


        <div class="whole-wrap">
            <div class="container box_1170">
                <div class="section-top-border">

                    @if ($this->selectedRoom['name'] === 'Function Hall')
                        {{-- <h3>Image Gallery</h3> --}}
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
                        {{-- <div class="instragram_area">
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
                        </div> --}}
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
                        @php $available = $record['standardAvailable'] - $record['standardOccupied']; @endphp
                        <div class="text-right"> {{ $available }} Available Room(s) Today</div>
                        {{ $this->standardSuiteForm }}
                    @break

                    @case('Deluxe Suite')
                        @php $available = $record['deluxeAvailable'] - $record['deluxeOccupied']; @endphp
                        <div class="text-right"> {{ $available }} Available Room(s) Today</div>
                        {{ $this->deluxeSuiteForm }}
                    @break

                    @case('Executive Suite')
                        @php $available = $record['executiveAvailable'] - $record['executiveOccupied']; @endphp
                        <div class="text-right"> {{ $available }} Available Room(s) Today</div>
                        {{ $this->executiveSuiteForm }}
                    @break
                @endswitch

                {{-- @if ($available > 0) --}}
                <div class="text-right mt-4">
                    <div class="book_btn">
                        <div x-data="{ booked: false }" x-on:reset-book-button.window="booked = false">
                            <button type="button" class="genric-btn info" x-show="!booked"
                                wire:click.prevent="bookRoom" wire:loading.attr="disabled" wire:target="bookRoom"
                                @click="booked = true">

                                <span wire:loading.remove wire:target="bookRoom">
                                    Book Now
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                {{-- @endif --}}
    @endif

    @auth
        <h3 class="text-heading mt-3">Calendar</h3>
        <div style="height: 300px;" id="calendar" class="p-4 bg-white rounded-lg shadow" x-data="{
            events: {{ Js::from($this->calendarEvents) }},
            initCalendar() {
                const calendarEl = document.getElementById('calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: this.events,
        
                    // --- Add these new options for tooltips ---
                    eventMouseEnter: function(info) {
                        tippy(info.el, {
                            content: info.event.title, // Use the event's title as the tooltip content
                            placement: 'top',
                            animation: 'shift-away',
                        });
                    },
                    eventMouseLeave: function(info) {
                        if (info.el._tippy) {
                            info.el._tippy.destroy();
                        }
                    }
                    // --- End of new options ---
        
                });
                calendar.render();
            }
        }"
            x-init="initCalendar()">
        </div>
    @endauth


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
<script src="https://unpkg.com/popper.js@1"></script>
<script src="https://unpkg.com/tippy.js@5"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>


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

<script>
    // Initialize Function Hall carousel
    document.addEventListener('DOMContentLoaded', function() {
        function initFunctionHallCarousel() {
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.owlCarousel !== 'undefined') {
                var carousel = jQuery('.functionhall-carousel');
                if (carousel.length && !carousel.hasClass('owl-loaded')) {
                    carousel.owlCarousel({
                        loop: true,
                        margin: 15,
                        items: 1,
                        autoplay: true,
                        autoplayTimeout: 3000,
                        autoplayHoverPause: true,
                        nav: true,
                        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                        dots: true,
                        responsive: {
                            0: {
                                items: 1,
                                nav: false
                            },
                            600: {
                                items: 2,
                                nav: false
                            },
                            992: {
                                items: 3,
                                nav: true
                            }
                        }
                    });
                }
            }
        }
        
        // Initialize Suite Images carousel
        function initSuiteImagesCarousel() {
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.owlCarousel !== 'undefined') {
                var carousel = jQuery('.suite-images-carousel');
                if (carousel.length && !carousel.hasClass('owl-loaded')) {
                    carousel.owlCarousel({
                        loop: true,
                        margin: 15,
                        items: 1,
                        autoplay: true,
                        autoplayTimeout: 3000,
                        autoplayHoverPause: true,
                        nav: true,
                        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
                        dots: true,
                        responsive: {
                            0: {
                                items: 1,
                                nav: false
                            },
                            600: {
                                items: 2,
                                nav: false
                            },
                            992: {
                                items: 3,
                                nav: true
                            }
                        }
                    });
                }
            }
        }
        
        // Initialize on page load
        setTimeout(initFunctionHallCarousel, 500);
        setTimeout(initSuiteImagesCarousel, 500);
        
        // Re-initialize when Livewire updates
        if (typeof Livewire !== 'undefined') {
            Livewire.hook('morph.updated', () => {
                setTimeout(initFunctionHallCarousel, 500);
                setTimeout(initSuiteImagesCarousel, 500);
            });
        }
    });
</script>
