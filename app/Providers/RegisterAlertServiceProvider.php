<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\StubNotificationService;
use App\Contracts\RegisterAlertServiceInterface;

class RegisterAlertServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //This binds our stub service to the contract/interface to it can be accessed by reference to the interface
        $this->app->bind(RegisterAlertServiceInterface::class, StubNotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        //UserObserver will ensure an API key is generated on creation
        \App\Models\User::observe(\App\Observers\UserObserver::class);
    }
}