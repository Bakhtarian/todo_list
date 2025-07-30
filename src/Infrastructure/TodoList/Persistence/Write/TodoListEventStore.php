<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Write;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\TodoList\Event\TodoListEventStoreInterface;
use App\Domain\TodoList\TodoList;
use App\Infrastructure\Shared\Persistence\Write\AbstractDbalEventStore;
use Doctrine\DBAL\Connection;

/**
 * @phpstan-type serializedData array{
 *      uuid: string,
 *      playhead: int,
 *      payload: non-empty-string,
 *      meta_data: non-empty-string,
 *      recorded_at: string,
 *      type: class-string<EventInterface>,
 * }
 *
 * @template-extends AbstractDbalEventStore<TodoList>
 */
final readonly class TodoListEventStore extends AbstractDbalEventStore implements TodoListEventStoreInterface
{
    private const string TABLE_NAME = 'todo_list_events';

    public function __construct(
        Connection $connection,
    ) {
        parent::__construct(
            connection: $connection,
            serializer: new TodoListMessageToAggregateRootSerializer(),
            tableName: self::TABLE_NAME,
            modelFQCN: TodoList::class,
        );
    }
}
