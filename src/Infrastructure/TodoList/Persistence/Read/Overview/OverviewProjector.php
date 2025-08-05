<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\Overview;

use App\Domain\Shared\Exception\CouldNotFindReadModelException;
use App\Domain\Shared\Exception\CouldNotSaveReadModelException;
use App\Domain\Shared\Exception\TooManyResultsException;
use App\Domain\Shared\Persistence\Read\AbstractReadModelProjector;
use App\Domain\TodoList\Event\DescriptionForToDoListWasAdded;
use App\Domain\TodoList\Event\DescriptionForTodoListWasAdjusted;
use App\Domain\TodoList\Event\DescriptionForTodoListWasRemoved;
use App\Domain\TodoList\Event\TodoListWithTitleWasCreated;

/**
 * @template-extends AbstractReadModelProjector<Overview, OverviewRepository>
 */
final readonly class OverviewProjector extends AbstractReadModelProjector
{
    public function __construct(OverviewRepository $repository)
    {
        parent::__construct(repository: $repository);
    }

    /**
     * @throws CouldNotSaveReadModelException
     */
    public function handleTodoListWithTitleWasCreated(TodoListWithTitleWasCreated $event): void
    {
        $readModel = new Overview(
            id: (string) $event->aggregateRootId,
            title: $event->title,
            createdAt: $event->createdAt,
        );

        $this->repository->save(identifiableModel: $readModel);
    }

    /**
     * @throws TooManyResultsException
     * @throws CouldNotFindReadModelException
     */
    public function handleDescriptionForToDoListWasAdded(DescriptionForToDoListWasAdded $event): void
    {
        $readModel = $this->getReadModel(id: (string) $event->aggregateRootId);

        $this->repository
            ->update(
                identifiableModel: $readModel->update(
                    updatedAt: $event->updatedAt
                )
            );
    }

    /**
     * @throws TooManyResultsException
     * @throws CouldNotFindReadModelException
     */
    public function handleDescriptionForTodoListWasAdjusted(DescriptionForTodoListWasAdjusted $event): void
    {
        $readModel = $this->getReadModel(id: (string) $event->aggregateRootId);

        $this->repository
            ->update(
                identifiableModel: $readModel->update(
                    updatedAt: $event->updatedAt
                )
            );
    }

    /**
     * @throws TooManyResultsException
     * @throws CouldNotFindReadModelException
     */
    public function handleDescriptionForTodoListWasRemoved(DescriptionForTodoListWasRemoved $event): void
    {
        $readModel = $this->getReadModel(id: (string) $event->aggregateRootId);

        $this->repository
            ->update(
                identifiableModel: $readModel->update(
                    updatedAt: $event->updatedAt
                )
            );
    }
}
