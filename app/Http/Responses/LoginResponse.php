<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends \Filament\Http\Responses\Auth\LoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        if (Auth::check()) {

            $user = auth()->user();

            // 2. If user is ADMIN → skip 2FA, go to Filament
            if (! $user->isCustomer()) {
                return redirect()->intended(\Filament\Facades\Filament::getUrl());
            }

            // 4. Regular authenticated user → require 2FA
            if ($user->isCustomer()) {
                $user->generateCode();
            }

            return redirect()->route('2fa.index');
        }

        return parent::toResponse($request);
    }
}
