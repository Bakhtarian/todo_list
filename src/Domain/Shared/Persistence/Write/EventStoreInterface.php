<?php

namespace App\Domain\Shared\Persistence\Write;

use App\Domain\Shared\Aggregate\AggregateRootInterface;
use App\Domain\Shared\Event\EventCriteriaInterface;
use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\CouldNotFindEventStreamException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\Message\MessageInterface;
use App\Domain\Shared\Message\MessageStreamInterface;

/**
 * @template T of AggregateRootInterface
 */
interface EventStoreInterface
{
    /**
     * @phpstan-param MessageStreamInterface<MessageInterface<EventInterface>> $stream
     */
    public function append(\Stringable $aggregate, MessageStreamInterface $stream): void;

    /**
     * @phpstan-return T
     *
     * @throws InvalidAggregateStringProvidedException
     * @throws MissingMethodToApplyEventException
     * @throws InvalidUuidStringProvidedException
     * @throws DateTimeException
     * @throws CouldNotFindEventStreamException
     */
    public function load(\Stringable $aggregateRootId): AggregateRootInterface;

    /**
     * @phpstan-return T
     */
    public function loadFromPlayhead(\Stringable $aggregateRootId, int $playhead): AggregateRootInterface;

    /**
     * @return MessageInterface<EventInterface>[]
     */
    public function loadAll(): array;
}
