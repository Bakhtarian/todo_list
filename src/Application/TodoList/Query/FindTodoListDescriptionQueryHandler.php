<?php

declare(strict_types=1);

namespace App\Application\TodoList\Query;

use App\Domain\Shared\Query\QueryHandlerInterface;
use App\Domain\Shared\Query\QueryInterface;
use App\Domain\TodoList\Persistence\Read\FindDescriptionByAggregateInterface;
use App\Domain\TodoList\ValueObject\Description;

/**
 * @template-implements QueryHandlerInterface<FindTodoListDescriptionQuery, Description|null>
 */
final readonly class FindTodoListDescriptionQueryHandler implements QueryHandlerInterface
{
    public function __construct(private FindDescriptionByAggregateInterface $repository)
    {
    }

    public function __invoke(QueryInterface $query): ?Description
    {
        return $this->repository->findDescription(aggregateRootId: $query->aggregateRootId);
    }
}
