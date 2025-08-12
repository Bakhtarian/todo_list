<?php

declare(strict_types=1);

namespace App\Tests\Util\InMemory;

use App\Domain\Shared\Aggregate\AggregateRootInterface;
use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\CouldNotFindEventStreamException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\Message\MessageInterface;
use App\Domain\Shared\Message\MessageSerializer;
use App\Domain\Shared\Message\MessageSerializerInterface;
use App\Domain\Shared\Message\MessageStream;
use App\Domain\Shared\Message\MessageStreamInterface;
use App\Domain\Shared\Persistence\Write\EventStoreInterface;

/**
 * @phpstan-import-type serializedData from MessageSerializer
 *
 * @template-implements EventStoreInterface<AggregateRootInterface>
 */
final class InMemoryEventStore implements EventStoreInterface
{
    /**
     * @phpstan-param array<int, serializedData> $events
     * @phpstan-param MessageSerializerInterface<MessageInterface<EventInterface>>|MessageSerializer $messageSerializer
     */
    public function __construct(
        private array $events = [],
        private readonly string $modelFQCN = '',
        private readonly string $aggregateRootColumn = 'uuid',
        private readonly MessageSerializerInterface $messageSerializer = new MessageSerializer(),
    ) {
    }

    public function append(
        \Stringable $aggregate,
        MessageStreamInterface $stream,
    ): void {
        foreach ($stream->getMessages() as $message) {
            /** @var serializedData $serialized */
            $serialized = $this->messageSerializer->serialize(message: $message);

            $this->events[] = $serialized;
        }
    }

    public function load(\Stringable $aggregateRootId): AggregateRootInterface
    {
        return $this->loadFromPlayhead(aggregateRootId: $aggregateRootId, playhead: 0);
    }

    /**
     * @throws DateTimeException
     * @throws ValueObjectDidNotMeetValidationException
     * @throws CouldNotFindEventStreamException
     */
    public function loadFromPlayhead(
        \Stringable $aggregateRootId,
        int $playhead,
    ): AggregateRootInterface {
        $loadedEvents = [];

        foreach ($this->events as $event) {
            if (
                $event[$this->aggregateRootColumn] === (string) $aggregateRootId
                && $event['playhead'] >= $playhead
            ) {
                $loadedEvents[] = $this->messageSerializer->deserialize(data: $event);
            }
        }

        if (empty($loadedEvents)) {
            throw CouldNotFindEventStreamException::forModel(
                modelFQCN: $this->modelFQCN,
                aggregateRootId: (string) $aggregateRootId,
            );
        }

        $messageStream = new MessageStream(messages: $loadedEvents);
        /** @var AggregateRootInterface $aggregateRoot */
        $aggregateRoot = new $this->modelFQCN();
        $aggregateRoot->reconstructFromStream(stream: $messageStream);

        return $aggregateRoot;
    }

    /**
     * @throws DateTimeException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function loadAllMessages(): array
    {
        $messages = [];

        foreach ($this->events as $event) {
            $messages[] = $this->messageSerializer->deserialize(data: $event);
        }

        return $messages;
    }
}
