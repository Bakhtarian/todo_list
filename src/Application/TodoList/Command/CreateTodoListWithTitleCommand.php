<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;

final readonly class CreateTodoListWithTitleCommand implements CommandInterface
{
    /**
     * @param non-falsy-string $title
     */
    public function __construct(
        public AggregateRootId $id,
        public string $title,
    ) {
    }
}
