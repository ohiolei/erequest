<?php

namespace App\Listeners;

use App\Services\ActivityService;
use Illuminate\Auth\Events\Login;

class LogUserLogin
{
    public function __construct(protected ActivityService $activities) {}

    public function handle(Login $event): void
    {
        $this->activities->log('login', 'Logged in');
    }
}
