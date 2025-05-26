<?php

use App\Http\Controllers\SocialiteController;
use App\Livewire\TwoFactor;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::redirect('/', '/app');

Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');

Route::get('2fa', TwoFactor::class)->name('2fa.index')
    ->middleware('redirect2FA');
