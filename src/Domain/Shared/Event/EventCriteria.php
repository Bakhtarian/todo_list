<?php

declare(strict_types=1);

namespace App\Domain\Shared\Event;

use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\AggregateRootId;

/**
 * @template-implements EventCriteriaInterface<EventCriteria>
 */
final class EventCriteria implements EventCriteriaInterface
{
    /**
     * @var \Stringable[]
     */
    private(set) array $aggregateRootIds = [] {
        get => $this->aggregateRootIds;
        set => $value;
    }

    /**
     * @var string[]
     */
    private(set) array $eventTypes = [] {
        get => $this->eventTypes;
        set => $value;
    }
    /**
     * @var \Stringable[]
     */
    private(set) array $aggregateRootIdsToIgnore = [] {
        get => $this->aggregateRootIdsToIgnore;
        set => $value;
    }

    /**
     * @var string[]
     */
    private(set) array $eventTypesToIgnore = [] {
        get => $this->eventTypesToIgnore;
        set => $value;
    }

    /**
     * @phpstan-param array<\Stringable> $aggregateRootIds
     * @phpstan-param array<string> $eventTypes
     * @phpstan-param array<\Stringable> $aggregateRootIdsToIgnore
     * @phpstan-param array<string> $eventTypesToIgnore
     */
    public static function create(
        array $aggregateRootIds = [],
        array $eventTypes = [],
        array $aggregateRootIdsToIgnore = [],
        array $eventTypesToIgnore = [],
    ): self {
        $criteria = new self();

        $criteria->aggregateRootIds = $aggregateRootIds;
        $criteria->eventTypes = $eventTypes;
        $criteria->aggregateRootIdsToIgnore = $aggregateRootIdsToIgnore;
        $criteria->eventTypesToIgnore = $eventTypesToIgnore;

        return $criteria;
    }

    public function withAggregateRootId(AggregateRootId ...$aggregateRootIds): self
    {
        $this->aggregateRootIds = array_unique(
            array_merge(
                $this->aggregateRootIds,
                $aggregateRootIds
            )
        );

        return $this;
    }

    public function withAggregateRootIdToIgnore(AggregateRootId ...$aggregateRootIds): self
    {
        $this->aggregateRootIdsToIgnore = array_unique(
            array_merge(
                $this->aggregateRootIdsToIgnore,
                $aggregateRootIds
            )
        );

        return $this;
    }

    /**
     * @phpstan-param non-empty-string $aggregateRootIds
     *
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function withAggregateRootIdString(string ...$aggregateRootIds): self
    {
        return $this->withAggregateRootId(
            ...array_map(
                fn (string $aggregateRootId): AggregateRootId => AggregateRootId::fromString(value: $aggregateRootId),
                $aggregateRootIds
            )
        );
    }

    /**
     * @phpstan-param non-empty-string $aggregateRootIds
     *
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function withAggregateRootIdStringToIgnore(string ...$aggregateRootIds): self
    {
        return $this->withAggregateRootIdToIgnore(
            ...array_map(
                fn (string $aggregateRootId): AggregateRootId => AggregateRootId::fromString(value: $aggregateRootId),
                $aggregateRootIds
            )
        );
    }

    public function withEventTypes(string ...$eventTypes): self
    {
        $this->eventTypes = array_unique(
            array_merge(
                $this->eventTypes,
                $eventTypes
            )
        );

        return $this;
    }

    public function withEventTypesToIgnore(string ...$eventTypesToIgnore): self
    {
        $this->eventTypesToIgnore = array_unique(
            array_merge(
                $this->eventTypesToIgnore,
                $eventTypesToIgnore
            )
        );

        return $this;
    }

    public function isMatched(EventInterface $event): bool
    {
        if ($this->hasNoCriteria()) {
            return true;
        }

        $aggregateRootCheck = fn (\Stringable $aggregateRootId): bool => (string) $aggregateRootId === (string) $event->getAggregateId();
        $eventCheck = fn (string $eventType): bool => $eventType === $event::class;

        if (
            array_any(
                array: $this->eventTypesToIgnore,
                callback: $eventCheck,
            )
        ) {
            return false;
        }

        if (
            array_any(
                array: $this->aggregateRootIdsToIgnore,
                callback: $aggregateRootCheck
            )
        ) {
            return false;
        }

        if (
            !empty($this->eventTypes)
            && !array_any(
                array: $this->eventTypes,
                callback: $eventCheck,
            )
        ) {
            return false;
        }

        if (
            !empty($this->aggregateRootIds)
            && !array_any(
                array: $this->aggregateRootIds,
                callback: $aggregateRootCheck
            )
        ) {
            return false;
        }

        return true;
    }

    private function hasNoCriteria(): bool
    {
        return
            empty($this->aggregateRootIds)
            && empty($this->eventTypes)
            && empty($this->aggregateRootIdsToIgnore)
            && empty($this->eventTypesToIgnore)
        ;
    }
}
