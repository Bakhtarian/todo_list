<?php

namespace App\Domain\TodoList\Repository;

use App\Domain\Shared\Persistence\EventSourcedRepositoryInterface;
use App\Domain\TodoList\TodoList;

/**
 * @template-extends EventSourcedRepositoryInterface<TodoList>
 */
interface TodoListRepositoryInterface extends EventSourcedRepositoryInterface
{
}
