<?php

namespace App\Domain\Shared\Event;

/**
 * @template T
 */
interface EventCriteriaInterface
{
    public function isMatched(EventInterface $event): bool;
}
