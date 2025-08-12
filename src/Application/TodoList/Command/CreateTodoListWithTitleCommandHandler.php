<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandHandlerInterface;
use App\Domain\Shared\Exception\BusinessRuleValidationException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\Persistence\EventSourcedRepositoryInterface;
use App\Domain\TodoList\Specification\Checker\TitleUniquenessCheckerInterface;
use App\Domain\TodoList\TodoList;

final readonly class CreateTodoListWithTitleCommandHandler implements CommandHandlerInterface
{
    /**
     * @param EventSourcedRepositoryInterface<TodoList>  $todoListRepository
     */
    public function __construct(
        private EventSourcedRepositoryInterface $todoListRepository,
        private TitleUniquenessCheckerInterface $titleUniquenessChecker,
    ) {
    }

    /**
     * @throws BusinessRuleValidationException
     * @throws MissingMethodToApplyEventException
     * @throws ValueObjectDidNotMeetValidationException
     * @throws DateTimeException
     */
    public function __invoke(CreateTodoListWithTitleCommand $command): void
    {
        $todoList = TodoList::createWithTitle(
            id: $command->aggregateRootId,
            title: $command->title,
            titleUniquenessChecker: $this->titleUniquenessChecker,
        );

        $this->todoListRepository->save(aggregateRoot: $todoList);
    }
}
