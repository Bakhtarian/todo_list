<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\Overview;

use App\Domain\Shared\Exception\CouldNotFindReadModelException;
use App\Domain\Shared\Exception\CouldNotSaveReadModelException;
use App\Domain\Shared\Exception\TooManyResultsException;
use App\Domain\Shared\Persistence\Read\AbstractReadModelProjector;
use App\Domain\Shared\Persistence\ReadModelRepositoryInterface;
use App\Domain\TodoList\Event\DescriptionForToDoListWasAdded;
use App\Domain\TodoList\Event\DescriptionForTodoListWasAdjusted;
use App\Domain\TodoList\Event\DescriptionForTodoListWasRemoved;
use App\Domain\TodoList\Event\TodoListWithTitleWasCreated;

/**
 * @template-extends AbstractReadModelProjector<Overview>
 */
final readonly class OverviewProjector extends AbstractReadModelProjector
{
    /**
     * @param ReadModelRepositoryInterface<Overview> $overviewRepository
     */
    public function __construct(ReadModelRepositoryInterface $overviewRepository)
    {
        parent::__construct(repository: $overviewRepository);
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
