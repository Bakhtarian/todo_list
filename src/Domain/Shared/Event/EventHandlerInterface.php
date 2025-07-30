<?php

namespace App\Domain\Shared\Event;

/**
 * @template T of EventInterface
 */
interface EventHandlerInterface
{
    /**
     * @phpstan-param T $event
     */
    public function handle(EventInterface $event): void;
}
