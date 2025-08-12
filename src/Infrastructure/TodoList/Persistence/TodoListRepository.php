<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence;

use App\Domain\Shared\Persistence\AbstractEventSourcedRepository;
use App\Domain\Shared\Persistence\Write\EventStoreInterface;
use App\Domain\TodoList\TodoList;
use App\Infrastructure\Shared\Bus\EventBus;

/**
 * @template T of TodoList
 *
 * @template-extends AbstractEventSourcedRepository<T>
 */
final readonly class TodoListRepository extends AbstractEventSourcedRepository
{
    /**
     * @param EventStoreInterface<T> $todoListEventStore
     */
    public function __construct(
        EventStoreInterface $todoListEventStore,
        EventBus $eventBus,
    ) {
        parent::__construct(
            eventStore: $todoListEventStore,
            eventBus: $eventBus,
        );
    }
}
