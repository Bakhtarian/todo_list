<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\DetailView;

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
 * @template-extends AbstractReadModelProjector<DetailView>
 */
final readonly class DetailViewProjector extends AbstractReadModelProjector
{
    /**
     * @param ReadModelRepositoryInterface<DetailView> $detailViewRepository
     */
    public function __construct(ReadModelRepositoryInterface $detailViewRepository)
    {
        parent::__construct(repository: $detailViewRepository);
    }

    /**
     * @throws CouldNotSaveReadModelException
     */
    public function handleTodoListWithTitleWasCreated(TodoListWithTitleWasCreated $event): void
    {
        $readModel = new DetailView(
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
                identifiableModel: $readModel->addDescription(
                    description: $event->description
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
                identifiableModel: $readModel->adjustDescription(
                    description: $event->description
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
                identifiableModel: $readModel->removeDescription()
            );
    }
}
