<?php

namespace App\Livewire;

use App\Models\UserCode;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Facades\Filament;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
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
                ->where('updated_at', '>=', now()->subMinutes(2))
                ->first();

            if ($find) {
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
}
