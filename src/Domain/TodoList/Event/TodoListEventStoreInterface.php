<?php

namespace App\Domain\TodoList\Event;

use App\Domain\Shared\Persistence\Write\EventStoreInterface;
use App\Domain\TodoList\TodoList;

/**
 * @template-extends EventStoreInterface<TodoList>
 */
interface TodoListEventStoreInterface extends EventStoreInterface
{
}
