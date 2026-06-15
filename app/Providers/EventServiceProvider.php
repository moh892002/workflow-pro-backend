<?php

namespace App\Providers;

use App\Events\ActivityLogged;
use App\Listeners\StoreActivityLog;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ActivityLogged::class => [
            StoreActivityLog::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
