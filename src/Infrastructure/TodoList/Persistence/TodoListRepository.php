<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence;

use App\Domain\Shared\Persistence\AbstractEventSourcedRepository;
use App\Domain\TodoList\Event\TodoListEventStoreInterface;
use App\Domain\TodoList\Persistence\TodoListRepositoryInterface;
use App\Domain\TodoList\TodoList;
use App\Infrastructure\Shared\Bus\EventBus;

/**
 * @template-extends AbstractEventSourcedRepository<TodoList>
 */
final readonly class TodoListRepository extends AbstractEventSourcedRepository implements TodoListRepositoryInterface
{
    public function __construct(
        TodoListEventStoreInterface $eventStore,
        EventBus $eventBus,
    ) {
        parent::__construct(
            eventStore: $eventStore,
            eventBus: $eventBus,
        );
    }
}
