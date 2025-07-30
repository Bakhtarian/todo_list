<?php

declare(strict_types=1);

namespace App\Domain\TodoList\Event;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\Shared\ValueObject\DateTime;

/**
 * @phpstan-type TodoListWithTitleWasCreatedData array{
 *     id: non-empty-string,
 *     title: non-falsy-string,
 *     createdAt: string,
 * }
 */
final readonly class TodoListWithTitleWasCreated implements EventInterface
{
    /**
     * @param non-falsy-string $title
     */
    public function __construct(
        public AggregateRootId $id,
        public string $title,
        public DateTime $createdAt,
    ) {
    }

    /**
     * @phpstan-return TodoListWithTitleWasCreatedData
     */
    public function serialize(): array
    {
        return [
            'id' => $this->id->toString(),
            'title' => $this->title,
            'createdAt' => $this->createdAt->toString(),
        ];
    }

    /**
     * @param TodoListWithTitleWasCreatedData $data
     *
     * @throws DateTimeException
     * @throws InvalidAggregateStringProvidedException
     * @throws InvalidUuidStringProvidedException
     */
    public static function deserialize(array $data): TodoListWithTitleWasCreated
    {
        return new self(
            id: AggregateRootId::fromString($data['id']),
            title: $data['title'],
            createdAt: DateTime::fromString(dateTime: $data['createdAt']),
        );
    }

    public function getAggregateId(): \Stringable
    {
        return $this->id;
    }
}
