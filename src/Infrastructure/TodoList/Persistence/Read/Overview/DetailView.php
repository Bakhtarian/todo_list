<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\Overview;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Persistence\Read\SerializableReadModelInterface;
use App\Domain\Shared\ValueObject\DateTime;

/**
 * @phpstan-type detailViewData array{
 *      uuid: string,
 *      title: string,
 *      createdAt: string,
 *      description: string|null,
 *      deadline: string|null,
 *      finished: bool,
 *  }
 *
 * @template-implements SerializableReadModelInterface<DetailView>
 */
final class DetailView implements SerializableReadModelInterface
{
    public function __construct(
        private(set) string $id {
            set => $value;
            get => $this->id;
        },
        private(set) string $title {
            set => $value;
            get => $this->title;
        },
        private(set) DateTime $createdAt {
            set => $value;
            get => $this->createdAt;
        },
        private(set) ?string $description = null {
            set => $value;
            get => $this->description;
        },
        private(set) ?DateTime $deadline = null {
            set => $value;
            get => $this->deadline;
        },
        private(set) bool $finished = false {
            set => $value;
            get => $this->finished;
        },
    ) {
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return detailViewData
     */
    public function serialize(): array
    {
        return [
            'uuid' => $this->getId(),
            'title' => $this->title,
            'createdAt' => $this->createdAt->toString(),
            'description' => $this->description,
            'deadline' => $this->deadline?->toString(),
            'finished' => $this->finished,
        ];
    }

    /**
     * @param detailViewData $data
     *
     * @throws DateTimeException
     */
    public static function deserialize(array $data): DetailView
    {
        return new self(
            id: $data['uuid'],
            title: $data['title'],
            createdAt: DateTime::fromString(dateTime: $data['createdAt']),
            description: $data['description'] ?? null,
            deadline: DateTime::tryFromString(dateTime: $data['deadline'] ?? null),
            finished: $data['finished'],
        );
    }

    public function getId(): string
    {
        return $this->id;
    }
}
