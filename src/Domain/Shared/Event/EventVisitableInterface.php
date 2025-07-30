<?php

namespace App\Domain\Shared\Event;

interface EventVisitableInterface
{
    public function visitEventsWithCriteria(EventCriteriaInterface $criteria): void;
}
