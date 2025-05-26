<?php

namespace App\Providers;

use App\Http\Responses\LogoutResponse;
use Filament\Http\Responses\Auth\Contracts\LoginResponse as ContractsLoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->singleton(
            ContractsLoginResponse::class,
            \App\Http\Responses\LoginResponse::class
        );

        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);

        FilamentColor::register([
            'primary' => Color::Teal,
        ]);

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => ucfirst(auth()->user()->role),
        );
    }
}
