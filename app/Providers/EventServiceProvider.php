<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected array $listen = [
        Login::class => [
            \App\Listeners\LogUserLogin::class,
        ],
        Logout::class => [
            \App\Listeners\LogUserLogout::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
