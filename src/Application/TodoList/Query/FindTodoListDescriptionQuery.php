<?php

declare(strict_types=1);

namespace App\Application\TodoList\Query;

use App\Domain\Shared\Query\QueryInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;

/**
 * @template-implements QueryInterface<FindTodoListDescriptionQuery>
 */
final readonly class FindTodoListDescriptionQuery implements QueryInterface
{
    public function __construct(
        public AggregateRootId $aggregateRootId,
    ) {
    }
}
