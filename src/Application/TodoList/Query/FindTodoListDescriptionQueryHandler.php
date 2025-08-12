<?php

declare(strict_types=1);

namespace App\Application\TodoList\Query;

use App\Domain\Shared\Query\QueryHandlerInterface;
use App\Domain\TodoList\Persistence\Read\FindDescriptionByAggregateInterface;
use App\Domain\TodoList\ValueObject\Description;

final readonly class FindTodoListDescriptionQueryHandler implements QueryHandlerInterface
{
    public function __construct(private FindDescriptionByAggregateInterface $repository)
    {
    }

    public function __invoke(FindTodoListDescriptionQuery $query): ?Description
    {
        return $this->repository->findDescription(aggregateRootId: $query->aggregateRootId);
    }
}
