<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Replay;

use App\Domain\Shared\Event\EventBusInterface;
use App\Domain\Shared\Replay\Replay;
use App\Domain\TodoList\Event\TodoListEventStoreInterface;
use App\Domain\TodoList\TodoList;

/**
 * @template-extends Replay<TodoList>
 */
final class ReplayTodoListAggregate extends Replay
{
    public function __construct(
        TodoListEventStoreInterface $eventStore,
        EventBusInterface $eventBus,
    ) {
        parent::__construct(
            eventStore: $eventStore,
            eventBus: $eventBus,
        );
    }
}
