<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandHandlerInterface;
use App\Domain\Shared\Command\CommandInterface;
use App\Domain\Shared\Exception\BusinessRuleValidationException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\TodoList\Persistence\TodoListRepositoryInterface;
use App\Domain\TodoList\Specification\Checker\TitleUniquenessCheckerInterface;
use App\Domain\TodoList\TodoList;

final readonly class CreateTodoListWithTitleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private TodoListRepositoryInterface $repository,
        private TitleUniquenessCheckerInterface $titleUniquenessChecker,
    ) {
    }

    /**
     * @throws BusinessRuleValidationException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function handle(CommandInterface $command): void
    {
        if (!$command instanceof CreateTodoListWithTitleCommand) {
            return;
        }

        $todoList = TodoList::createWithTitle(
            id: $command->aggregateRootId,
            title: $command->title,
            titleUniquenessChecker: $this->titleUniquenessChecker,
        );

        $this->repository->save(aggregateRoot: $todoList);
    }
}
