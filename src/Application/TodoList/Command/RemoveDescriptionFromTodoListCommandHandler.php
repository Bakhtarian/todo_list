<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandHandlerInterface;
use App\Domain\Shared\Command\CommandInterface;
use App\Domain\TodoList\Persistence\TodoListRepositoryInterface;

final readonly class RemoveDescriptionFromTodoListCommandHandler implements CommandHandlerInterface
{
    public function __construct(private TodoListRepositoryInterface $repository)
    {
    }

    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof RemoveDescriptionFromTodoListCommand) {
            return;
        }

        $list = $this->repository->load(aggregateRootId: $command->aggregateRootId);
        $list->removeDescription();

        $this->repository->save(aggregateRoot: $list);
    }
}
