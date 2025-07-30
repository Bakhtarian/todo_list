<?php

declare(strict_types=1);

namespace App\Domain\TodoList\Event;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\ValueObject\AggregateRootId;

/**
 * @phpstan-type descriptionWasSetData array{
 *     id: string,
 *     description: string|null
 * }
 */
final readonly class DescriptionWasSet implements EventInterface
{
    public function __construct(
        public AggregateRootId $id,
        public ?string $description,
    ) {
    }

    /**
     * @return descriptionWasSetData
     */
    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'description' => $this->description,
        ];
    }

    /**
     * @phpstan-param descriptionWasSetData $data
     *
     * @throws InvalidAggregateStringProvidedException
     * @throws InvalidUuidStringProvidedException
     */
    public static function deserialize(array $data): DescriptionWasSet
    {
        return new self(
            id: AggregateRootId::fromString(uuid: $data['id']),
            description: $data['description']
        );
    }

    public function getAggregateId(): \Stringable
    {
        return $this->id;
    }
}
