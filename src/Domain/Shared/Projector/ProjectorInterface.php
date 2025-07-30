<?php

namespace App\Domain\Shared\Projector;

use App\Domain\Shared\Event\EventInterface;

/**
 * @template T of EventInterface
 */
interface ProjectorInterface
{
    /**
     * @phpstan-param T $message
     */
    public function handle(EventInterface $message): void;
}
