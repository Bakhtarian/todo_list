<?php

namespace App\Domain\TodoList\Persistence\Read;

use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\TodoList\ValueObject\Description;

interface FindDescriptionByAggregateInterface
{
    public function findDescription(AggregateRootId $aggregateRootId): ?Description;
}
