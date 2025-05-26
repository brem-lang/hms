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

    @filamentStyles
    {{-- @vite('resources/css/app.css') --}}
</head>

<body class="antialiased">
    {{ $slot }}

    @livewire('notifications')

    @filamentScripts
    {{-- @vite('resources/js/app.js') --}}
</body>

</html>
