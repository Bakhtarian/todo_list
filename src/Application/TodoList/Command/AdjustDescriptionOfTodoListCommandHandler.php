<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandHandlerInterface;
use App\Domain\Shared\Command\CommandInterface;
use App\Domain\TodoList\Persistence\TodoListRepositoryInterface;

final readonly class AdjustDescriptionOfTodoListCommandHandler implements CommandHandlerInterface
{
    public function __construct(private TodoListRepositoryInterface $repository)
    {
    }

    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof AdjustDescriptionOfTodoListCommand) {
            return;
        }

        $list = $this->repository->load(aggregateRootId: $command->aggregateRootId);
    }
}
