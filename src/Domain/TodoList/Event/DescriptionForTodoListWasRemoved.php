<?php

declare(strict_types=1);

namespace App\Domain\TodoList\Event;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\Shared\ValueObject\DateTime;

/**
 * @phpstan-type descriptionRemovedData array{
 *     id: non-empty-string,
 *     updatedAt: non-empty-string,
 * }
 */
final readonly class DescriptionForTodoListWasRemoved implements EventInterface
{
    public function __construct(
        public AggregateRootId $aggregateRootId,
        public DateTime $updatedAt,
    ) {
    }

    public function getAggregateId(): \Stringable
    {
        return $this->aggregateRootId;
    }

    /**
     * @return descriptionRemovedData
     */
    public function serialize(): array
    {
        return [
            'id' => $this->aggregateRootId->toString(),
            'updatedAt' => $this->updatedAt->toString(),
        ];
    }

    /**
     * @phpstan-param descriptionRemovedData $data
     *
     * @throws ValueObjectDidNotMeetValidationException
     * @throws DateTimeException
     */
    public static function deserialize(array $data): DescriptionForTodoListWasRemoved
    {
        return new self(
            aggregateRootId: AggregateRootId::fromString(value: $data['id']),
            updatedAt: DateTime::fromString(dateTime: $data['updatedAt']),
        );
    }
}
