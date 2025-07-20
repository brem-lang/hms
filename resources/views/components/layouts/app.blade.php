<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">

    <meta name="application-name" content="{{ config('app.name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>


    <style>
        .fi-simple-layout {
            width: 100%;
            height: 100%;
            background: url('http://hotel-management.test/images/background.jpg') center no-repeat;
            background-size: cover;
            background-attachment: fixed;
            top: 0;
            left: 0;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/filament/filament/app.css?v=3.2.124.0') }}">

    <!-- CSS here -->
    <link rel="stylesheet" href="{{ asset('css/customer/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/gijgo.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/slicknav.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/style.css') }}">

    <!-- JS here -->
    <script src="{{ asset('js/customer/vendor/modernizr-3.5.0.min.js') }}"></script>
    <script src="{{ asset('js/customer/vendor/jquery-1.12.4.min.js') }}"></script>
    <script src="{{ asset('js/customer/popper.min.js') }}"></script>
    <script src="{{ asset('js/customer/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/customer/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/customer/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('js/customer/ajax-form.js') }}"></script>
    <script src="{{ asset('js/customer/waypoints.min.js') }}"></script>
    <script src="{{ asset('js/customer/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('js/customer/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('js/customer/scrollIt.js') }}"></script>
    <script src="{{ asset('js/customer/jquery.scrollUp.min.js') }}"></script>
    <script src="{{ asset('js/customer/wow.min.js') }}"></script>
    <script src="{{ asset('js/customer/nice-select.min.js') }}"></script>
    <script src="{{ asset('js/customer/jquery.slicknav.min.js') }}"></script>
    <script src="{{ asset('js/customer/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('js/customer/plugins.js') }}"></script>
    <script src="{{ asset('js/customer/gijgo.min.js') }}"></script>

    <!--contact js-->
    <script src="{{ asset('js/customer/contact.js') }}"></script>
    <script src="{{ asset('js/customer/jquery.ajaxchimp.min.js') }}"></script>
    <script src="{{ asset('js/customer/jquery.form.js') }}"></script>
    <script src="{{ asset('js/customer/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('js/customer/mail-script.js') }}"></script>

    <script src="{{ asset('js/customer/main.js') }}"></script>

    <script>
        $('#datepicker').datepicker({
            iconsLibrary: 'fontawesome',
            icons: {
                rightIcon: '<span class="fa fa-caret-down"></span>'
            }
        });
        $('#datepicker2').datepicker({
            iconsLibrary: 'fontawesome',
            icons: {
                rightIcon: '<span class="fa fa-caret-down"></span>'
            }

        });
    </script>

    @filamentStyles
    {{-- @vite('resources/css/app.css') --}}
</head>

<body class="antialiased">
    {{ $slot }}

    @livewire('notifications')

    @filamentScripts
    @livewireScripts


    @vite('resources/js/app.js')
</body>

</html>
