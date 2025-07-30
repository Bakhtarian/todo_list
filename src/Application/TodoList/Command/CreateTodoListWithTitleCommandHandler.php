<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandHandlerInterface;
use App\Domain\Shared\Command\CommandInterface;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\TodoList\Repository\TodoListRepositoryInterface;
use App\Domain\TodoList\TodoList;

/**
 * @template-implements CommandHandlerInterface<CreateTodoListWithTitleCommand>
 */
final readonly class CreateTodoListWithTitleCommandHandler implements CommandHandlerInterface
{
    public function __construct(private TodoListRepositoryInterface $repository)
    {
    }

    /**
     * @throws InvalidAggregateStringProvidedException
     * @throws MissingMethodToApplyEventException
     * @throws InvalidUuidStringProvidedException
     * @throws DateTimeException
     */
    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof CreateTodoListWithTitleCommand) {
            return;
        }

        $todoList = TodoList::createWithTitle(
            id: $command->id,
            title: $command->title,
        );

        $this->repository->save(aggregateRoot: $todoList);
    }
}
