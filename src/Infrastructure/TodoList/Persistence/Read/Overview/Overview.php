<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\Overview;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\Persistence\Read\SerializableReadModelInterface;
use App\Domain\Shared\ValueObject\DateTime;
use App\Domain\TodoList\ValueObject\Title;

/**
 * @phpstan-type overviewData array{
 *     uuid: string,
 *     title: non-empty-string,
 *     createdAt: non-empty-string,
 *     updatedAt: non-empty-string|null
 * }
 *
 * @template-implements SerializableReadModelInterface<Overview>
 */
final class Overview implements SerializableReadModelInterface
{
    public function __construct(
        private(set) string $id {
            get => $this->id;
            set => $value;
        },
        private(set) Title $title {
            get => $this->title;
            set => $value;
        },
        private(set) DateTime $createdAt {
            get => $this->createdAt;
            set => $value;
        },
        private(set) ?DateTime $updatedAt = null {
            get => $this->updatedAt;
            set => $value;
        },
    ) {
    }

    public function update(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return overviewData
     */
    public function serialize(): array
    {
        return [
            'uuid' => $this->id,
            'title' => $this->title->toString(),
            'createdAt' => $this->createdAt->toString(),
            'updatedAt' => $this->updatedAt?->toString(),
        ];
    }

    /**
     * @phpstan-param overviewData $data
     *
     * @throws ValueObjectDidNotMeetValidationException
     * @throws DateTimeException
     */
    public static function deserialize(array $data): Overview
    {
        return new self(
            id: $data['uuid'],
            title: Title::fromString(value: $data['title']),
            createdAt: DateTime::fromString(dateTime: $data['createdAt']),
            updatedAt: DateTime::tryFromString(dateTime: $data['updatedAt'] ?? null),
        );
    }
}
