<?php

namespace App\Domain\Shared\Aggregate;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\IdentifiableInterface;
use App\Domain\Shared\Message\MessageInterface;
use App\Domain\Shared\Message\MessageStreamInterface;

interface AggregateRootInterface extends IdentifiableInterface
{
    public \Stringable $aggregateRootId { get; }

    /**
     * @param array<string, mixed> $withMetaData
     *
     * @throws MissingMethodToApplyEventException
     * @throws InvalidAggregateStringProvidedException
     * @throws InvalidUuidStringProvidedException
     */
    public function apply(
        EventInterface $event,
        array $withMetaData = [],
    ): void;

    /**
     * @template T of MessageInterface<EventInterface>
     *
     * @param MessageStreamInterface<T> $stream
     */
    public function reconstructFromStream(MessageStreamInterface $stream): void;

    /**
     * @return MessageStreamInterface<MessageInterface<EventInterface>>
     */
    public function getUncommittedMessages(): MessageStreamInterface;
}
