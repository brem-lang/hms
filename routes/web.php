<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\UserController;
use App\Livewire\CustomerPage;
use App\Livewire\MyBooking;
use App\Livewire\NewUserPrompt;
use App\Livewire\Policy;
use App\Livewire\TwoFactor;
use App\Livewire\ViewBooking;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
// Route::redirect('/', '/app');

Route::redirect('/login', '/app/login');

Route::redirect('/', '/index');

Route::get('/index', CustomerPage::class)->name('index');

Route::get('/new-prompt', NewUserPrompt::class)->name('new-prompt');

Route::get('policy', Policy::class)->name('policy');

Route::get('/my-bookings', MyBooking::class)->name('my-bookings')
    ->middleware('auth');

Route::get('/view-booking/{id}', ViewBooking::class)->name('view-booking');

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');

Route::get('/bookings/{booking}/charges-pdf', [BookingController::class, 'downloadChargesPdf'])
    ->name('bookings.charges.pdf');

Route::get('/reports/stream', [ReportController::class, 'streamReport'])
    ->name('reports.stream')
    ->middleware('auth');

Route::get('/verify-email/{id}/{hash}', [UserController::class, 'verifyEmail'])
    ->name('verification.verify');

Route::get('2fa', TwoFactor::class)->name('2fa.index')
    ->middleware('redirect2FA');
