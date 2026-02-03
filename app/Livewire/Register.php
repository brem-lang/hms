<?php

namespace App\Livewire;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Pages\Auth\Register as RegisterPage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Register extends RegisterPage
{
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('firstName')->required()->label('First Name'),
                        TextInput::make('lastName')->required()->label('Last Name'),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                        $this->getContactNumberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/register.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rules([
                Password::min(8) // Minimum length of 8 characters
                    ->mixedCase(), // Requires uppercase and lowercase letters
                'regex:/^(?=(.*\d){4,}).*$/', // Custom rule: At least 4 numeric characters
                'regex:/[!@#$%^&*(),.?":{}|<>]/', // Custom rule: At least one special character
            ])
            ->validationMessages([
                'regex' => 'The password must include at least 4 numeric characters and one special character.',
            ])
            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute'));
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false);
    }

    protected function getContactNumberFormComponent(): Component
    {
        return TextInput::make('contact_number')
            ->tel()->telRegex('/^(0|63)\d{10}$/')
            ->label('Contact Number')
            ->required();
    }

    protected function mutateFormDataBeforeRegister(array $data): array
    {
        $data['name'] = $data['firstName'].' '.$data['lastName'];
        $data['role'] = 'customer';

        unset($data['firstName']);

        unset($data['lastName']);

        return $data;
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(10);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        $this->sendEmailVerificationNotification($user);

        Filament::auth()->login($user);

        session()->regenerate();

        auth()->user()->generateCode();

        // Set registration flow flag to indicate this is a registration flow
        session()->put('registration_flow', true);

        return app(RegistrationResponse::class);
    }
}
