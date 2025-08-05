<?php

namespace App\Domain\Shared\Event;

interface EventVisitableInterface
{
    /**
     * @template T of EventCriteriaInterface
     *
     * @param EventCriteriaInterface<T> $criteria
     */
    public function visitEventsWithCriteria(EventCriteriaInterface $criteria): void;
}
