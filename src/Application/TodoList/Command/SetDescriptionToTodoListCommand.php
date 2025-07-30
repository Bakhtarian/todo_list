<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandInterface;

final readonly class SetDescriptionToTodoListCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public ?string $description,
    ) {
    }
}
