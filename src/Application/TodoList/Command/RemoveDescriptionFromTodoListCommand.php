<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;

final readonly class RemoveDescriptionFromTodoListCommand implements CommandInterface
{
    public function __construct(public AggregateRootId $aggregateRootId)
    {
    }
}
