<?php

declare(strict_types=1);

namespace App\Application\TodoList\Command;

use App\Domain\Shared\Command\CommandHandlerInterface;
use App\Domain\Shared\Exception\CouldNotFindEventStreamException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\Persistence\EventSourcedRepositoryInterface;
use App\Domain\TodoList\TodoList;

final readonly class AddDescriptionToTodoListCommandHandler implements CommandHandlerInterface
{
    /**
     * @param EventSourcedRepositoryInterface<TodoList>  $todoListRepository
     */
    public function __construct(private EventSourcedRepositoryInterface $todoListRepository)
    {
    }

    /**
     * @throws InvalidAggregateStringProvidedException
     * @throws CouldNotFindEventStreamException
     * @throws MissingMethodToApplyEventException
     * @throws ValueObjectDidNotMeetValidationException
     * @throws InvalidUuidStringProvidedException
     * @throws DateTimeException
     */
    public function __invoke(AddDescriptionToTodoListCommand $command): void
    {
        $list = $this->todoListRepository->load(aggregateRootId: $command->aggregateRootId);
        $list->addDescription(description: $command->description);

        $this->todoListRepository->save(aggregateRoot: $list);
    }
}
