<?php

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use Barryvdh\Debugbar\ServiceProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    ServiceProvider::class,
];
