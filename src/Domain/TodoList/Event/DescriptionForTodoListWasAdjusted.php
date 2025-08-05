<?php

declare(strict_types=1);

namespace App\Domain\TodoList\Event;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\Shared\ValueObject\DateTime;
use App\Domain\TodoList\ValueObject\Description;

/**
 * @phpstan-type descriptionForTodoListWasAdjustedData array{
 *     id: non-empty-string,
 *     description: non-empty-string,
 *     updatedAt: non-empty-string,
 * }
 */
final readonly class DescriptionForTodoListWasAdjusted implements EventInterface
{
    public function __construct(
        public AggregateRootId $aggregateRootId,
        public Description $description,
        public DateTime $updatedAt,
    ) {
    }

    public function getAggregateId(): \Stringable
    {
        return $this->aggregateRootId;
    }

    /**
     * @return descriptionForTodoListWasAdjustedData
     */
    public function serialize(): array
    {
        return [
            'id' => $this->aggregateRootId->toString(),
            'description' => $this->description->toString(),
            'updatedAt' => $this->updatedAt->toString(),
        ];
    }

    /**
     * @phpstan-param descriptionForTodoListWasAdjustedData $data
     *
     * @throws ValueObjectDidNotMeetValidationException
     * @throws DateTimeException
     */
    public static function deserialize(array $data): DescriptionForTodoListWasAdjusted
    {
        return new self(
            aggregateRootId: AggregateRootId::fromString(value: $data['id']),
            description: Description::fromString(value: $data['description']),
            updatedAt: DateTime::fromString(dateTime: $data['updatedAt']),
        );
    }
}
