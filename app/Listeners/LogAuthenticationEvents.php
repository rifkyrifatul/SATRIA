<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
use Spatie\Activitylog\Facades\Activity;

class LogAuthenticationEvents
{
    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleUserLogin',
            Logout::class => 'handleUserLogout',
        ];
    }

    /**
     * Handle user login events.
     */
    public function handleUserLogin(Login $event): void
    {
        if ($event->user) {
            Activity::causedBy($event->user)
                ->performedOn($event->user)
                ->event('login')
                ->log('User logged in');
        }
    }

    /**
     * Handle user logout events.
     */
    public function handleUserLogout(Logout $event): void
    {
        if ($event->user) {
            Activity::causedBy($event->user)
                ->performedOn($event->user)
                ->event('logout')
                ->log('User logged out');
        }
    }
}
