<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use App\Http\Responses\LogoutResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\LoginResponse as AuthLoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutContractResponse;

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
            AuthLoginResponse::class,
            LoginResponse::class,
        );
        Session::flush();

        $this->app->bind(
            LogoutContractResponse::class,
            LogoutResponse::class,
        );
    }
}
