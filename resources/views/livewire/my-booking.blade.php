<div x-data="{ open: false }">
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
                                        <li><a class="active" href="{{ route('my-bookings') }}">my bookings</a></li>
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
                                <button @click="open = true" class="btn btn-outline-secondary position-relative ">
                                    <i class="fa fa-bell fs-5"></i>
                                    @if ($notifications->count())
                                        <span
                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $notifications->count() }}
                                        </span>
                                    @endif
                                </button>
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
        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-light">
            <h5 class="mb-0">Notifications</h5>
            <button class="btn btn-sm btn-light" @click="open = false" aria-label="Close">
                <i class="fa fa-times"></i>
            </button>
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

    {{-- table area --}}

    @livewire('booking-table')
</div>
