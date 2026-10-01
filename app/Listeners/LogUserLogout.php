<?php

namespace App\Listeners;

use App\Services\ActivityService;
use Illuminate\Auth\Events\Logout;

class LogUserLogout
{
    public function __construct(protected ActivityService $activities) {}

    public function handle(Logout $event): void
    {
        if ($event->user) {
            $this->activities->log('logout', 'Logged out');
        }
    }
}
