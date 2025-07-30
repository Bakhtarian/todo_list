<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\Overview;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\CouldNotFindReadModelException;
use App\Domain\Shared\Exception\CouldNotSaveReadModelException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\Exception\TooManyResultsException;
use App\Domain\Shared\Projector\ProjectorInterface;
use App\Domain\TodoList\Event\DescriptionWasSet;
use App\Domain\TodoList\Event\TodoListWithTitleWasCreated;

/**
 * @template T of EventInterface
 *
 * @template-implements ProjectorInterface<T>
 */
final readonly class DetailViewProjector implements ProjectorInterface
{
    public function __construct(
        private DetailViewRepository $repository,
    ) {
    }

    /**
     * @throws MissingMethodToApplyEventException
     */
    public function handle(EventInterface $message): void
    {
        $eventToHandle = $message;
        $handleMethod = $this->getHandleMethod(event: $message);

        if (!method_exists($this, $handleMethod)) {
            return;
        }

        $this->{$handleMethod}(event: $eventToHandle);
    }

    /**
     * @throws CouldNotSaveReadModelException
     */
    public function handleTodoListWithTitleWasCreated(TodoListWithTitleWasCreated $event): void
    {
        $readModel = new DetailView(
            id: (string) $event->id,
            title: $event->title,
            createdAt: $event->createdAt,
        );

        $this->repository->save(identifiableModel: $readModel);
    }

    /**
     * @throws TooManyResultsException
     * @throws CouldNotFindReadModelException
     */
    public function handleDescriptionWasSet(DescriptionWasSet $event): void
    {
        $readModel = $this->getReadModel(id: (string) $event->id);

        $this->repository
            ->update(
                identifiableModel: $readModel->setDescription(
                    description: $event->description
                )
            );
    }

    /**
     * @throws TooManyResultsException
     * @throws CouldNotFindReadModelException
     */
    private function getReadModel(string $id): DetailView
    {
        $readModel = $this->repository->find(id: $id);

        if (null === $readModel) {
            throw new CouldNotFindReadModelException();
        }

        return $readModel;
    }

    private function getHandleMethod(EventInterface $event): string
    {
        $classParts = explode('\\', get_class($event));

        return 'handle' . end($classParts);
    }
}
