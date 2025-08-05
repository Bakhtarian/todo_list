<?php

namespace App\Domain\TodoList\Persistence;

use App\Domain\Shared\Persistence\EventSourcedRepositoryInterface;
use App\Domain\TodoList\TodoList;

/**
 * @template-extends EventSourcedRepositoryInterface<TodoList>
 */
interface TodoListRepositoryInterface extends EventSourcedRepositoryInterface
{
}
