<?php

namespace App\Domain\Shared\Event;

interface EventBusInterface
{
    public function publish(EventInterface $event): void;
}
