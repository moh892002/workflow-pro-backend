<?php

namespace App\Listeners;

use App\Events\ActivityLogged;
use Psr\Log\LoggerInterface;

class StoreActivityLog
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function handle(ActivityLogged $event): void
    {
        $this->logger->info('Activity logged', [
            'user_id' => $event->user->id,
            'action' => $event->action,
            'details' => $event->details,
        ]);
    }
}
