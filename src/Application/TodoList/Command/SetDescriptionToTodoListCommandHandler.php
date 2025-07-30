<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandHandlerInterface;
use App\Domain\Shared\Command\CommandInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\TodoList\Repository\TodoListRepositoryInterface;

/**
 * @template-implements CommandHandlerInterface<SetDescriptionToTodoListCommand>
 */
final readonly class SetDescriptionToTodoListCommandHandler implements CommandHandlerInterface
{
    public function __construct(private TodoListRepositoryInterface $repository)
    {
    }

    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof SetDescriptionToTodoListCommand) {
            return;
        }

        $list = $this->repository
            ->load(
                aggregateRootId: AggregateRootId::fromString(
                    uuid: $command->id
                )
            );

        $list->setDescription(description: $command->description);

        $this->repository->save(aggregateRoot: $list);
    }
}
