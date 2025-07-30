<?php

namespace App\Domain\Shared\Replay;

use App\Domain\Shared\Event\EventCriteriaInterface;
use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Message\MessageInterface;

interface ReplayInterface
{
    /**
     * @template T of EventCriteriaInterface
     *
     * @phpstan-param T $criteria
     */
    public function withCriteria(EventCriteriaInterface $criteria): static;

    public function replay(): void;
}
