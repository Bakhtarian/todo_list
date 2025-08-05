<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\TodoList\ValueObject\Description;

final readonly class AdjustDescriptionOfTodoListCommand implements CommandInterface
{
    public function __construct(
        public AggregateRootId $aggregateRootId,
        public Description $description,
    ) {
    }
}
