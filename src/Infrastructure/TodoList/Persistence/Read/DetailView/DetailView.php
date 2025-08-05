<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\DetailView;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\Persistence\Read\SerializableReadModelInterface;
use App\Domain\Shared\ValueObject\DateTime;
use App\Domain\TodoList\ValueObject\Description;
use App\Domain\TodoList\ValueObject\Title;

/**
 * @phpstan-type detailViewData array{
 *      uuid: string,
 *      title: non-empty-string,
 *      createdAt: string,
 *      description: non-empty-string|null,
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
        private(set) Title $title {
            set => $value;
            get => $this->title;
        },
        private(set) DateTime $createdAt {
            set => $value;
            get => $this->createdAt;
        },
        private(set) ?Description $description = null {
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

    public function addDescription(Description $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function removeDescription(): self
    {
        $this->description = null;

        return $this;
    }

    public function adjustDescription(Description $description): self
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
            'title' => $this->title->toString(),
            'createdAt' => $this->createdAt->toString(),
            'description' => $this->description?->toString(),
            'deadline' => $this->deadline?->toString(),
            'finished' => $this->finished,
        ];
    }

    /**
     * @phpstan-param detailViewData $data
     *
     * @throws DateTimeException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public static function deserialize(array $data): DetailView
    {
        return new self(
            id: $data['uuid'],
            title: Title::fromString(value: $data['title']),
            createdAt: DateTime::fromString(dateTime: $data['createdAt']),
            description: Description::tryFromString(value: $data['description']),
            deadline: DateTime::tryFromString(dateTime: $data['deadline'] ?? null),
            finished: $data['finished'],
        );
    }

    public function getId(): string
    {
        return $this->id;
    }
}
