<?php

namespace App\Livewire;

use App\Mail\TwoFactorMail;
use App\Models\UserCode;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class TwoFactor extends Component implements HasForms
{
    use InteractsWithForms, WithRateLimiting;

    public $otp;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.two-factor');
    }

    public function submit()
    {
        $data = $this->validate([
            'otp' => 'required|digits:6',
        ]);

        try {
            $this->rateLimit(5);

            session()->put('user_2fa', auth()->id());

            // if (auth()->user()->isCustomer()) {
            //     return redirect()->route('index');
            // } else {
            //     return redirect()->intended(Filament::getUrl());
            // }
            $find = UserCode::where('user_id', auth()->id())
                ->where('code', $data['otp'])
                ->where('updated_at', '>=', now()->subMinutes(1))
                ->first();

            if ($find) {
                // Check if this is a registration flow
                if (session()->has('registration_flow')) {
                    // Clear the registration flow flag
                    session()->forget('registration_flow');
                    
                    // Logout the user
                    Auth::logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    
                    // Redirect to login page
                    return redirect(Filament::getLoginUrl());
                }

                session()->put('user_2fa', auth()->id());

                if (auth()->user()->isCustomer() && ! auth()->user()->is_new_user) {
                    return redirect()->route('index');
                }

                if (auth()->user()->isCustomer() && auth()->user()->is_new_user) {
                    return redirect()->route('new-prompt');
                }

                if (! auth()->user()->isCustomer()) {
                    return redirect()->intended(Filament::getUrl());
                }
            } else {
                $this->dispatch('swal:success', [
                    'title' => 'Expired or Invalid Code',
                    'icon' => 'error',
                ]);
            }
        } catch (TooManyRequestsException $exception) {
            $this->dispatch('swal:success', [
                'title' => 'Too many attempts!',
                'icon' => 'error',
            ]);
        }
    }

    public function resend()
    {
        try {
            $this->rateLimit(5);
            $this->form->fill([]);

            $code = rand(100000, 999999);

            UserCode::updateOrCreate(
                ['user_id' => auth()->user()->id],
                ['code' => $code]
            );

            try {
                $details = [
                    'title' => 'Email from Millenium Suites',
                    'code' => $code,
                    'name' => auth()->user()->name,
                ];

                Mail::to(auth()->user()->email)->send(new TwoFactorMail($details));

                $this->dispatch('swal:success', [
                    'title' => 'New code has been successfully sent to your email.',
                    'icon' => 'success',
                ]);
            } catch (Exception $e) {
                logger('Error: '.$e->getMessage());
            }
        } catch (TooManyRequestsException $exception) {
            $this->dispatch('swal:success', [
                'title' => 'Too many attempts!',
                'icon' => 'error',
            ]);
        }
    }
}
