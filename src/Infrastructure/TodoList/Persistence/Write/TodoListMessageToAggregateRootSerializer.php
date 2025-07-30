<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Write;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\Message\Message;
use App\Domain\Shared\Message\MessageInterface;
use App\Domain\Shared\Message\MessageSerializer;
use App\Domain\Shared\Message\MessageSerializerInterface;
use App\Domain\Shared\Message\MessageStream;
use App\Domain\Shared\Message\MessageToAggregateRootSerializerInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\Shared\ValueObject\DateTime;
use App\Domain\TodoList\TodoList;

/**
 * @phpstan-type serializedData array{
 *       uuid: string,
 *       playhead: int,
 *       payload: non-empty-string,
 *       meta_data: non-empty-string,
 *       recorded_at: string,
 *       type: class-string<EventInterface>,
 *  }
 *
 * @template-implements MessageToAggregateRootSerializerInterface<TodoList>
 */
final readonly class TodoListMessageToAggregateRootSerializer implements MessageToAggregateRootSerializerInterface
{
    /**
     * @phpstan-param MessageSerializerInterface<MessageInterface<EventInterface>>|MessageSerializer $serializer
     */
    public function __construct(
        private MessageSerializerInterface $serializer = new MessageSerializer(),
    ) {
    }

    public function serialize(MessageInterface $message): array
    {
        return $this->serializer->serialize(message: $message);
    }

    /**
     * @param serializedData[] $data
     *
     * @throws DateTimeException
     * @throws InvalidAggregateStringProvidedException
     * @throws InvalidUuidStringProvidedException
     * @throws MissingMethodToApplyEventException
     */
    public function deserialize(array $data): TodoList
    {
        $messageStream = [];

        /** @phpstan-var serializedData $row */
        foreach ($data as $row) {
            $messageStream[] = $this->serializer->deserialize(data: $row);
        }

        $todoList = new TodoList();
        $todoList->reconstructFromStream(stream: new MessageStream(messages: $messageStream));

        return $todoList;
    }

    public function withSerializer(MessageSerializerInterface $serializer): TodoListMessageToAggregateRootSerializer
    {
        return new self(serializer: $serializer);
    }
}
