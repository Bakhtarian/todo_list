<?php

declare(strict_types=1);

namespace App\Domain\TodoList;

use App\Domain\Shared\Aggregate\AggregateRootBehaviourTrait;
use App\Domain\Shared\Aggregate\AggregateRootInterface;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\Shared\ValueObject\DateTime;
use App\Domain\TodoList\Event\DescriptionWasSet;
use App\Domain\TodoList\Event\TodoListWithTitleWasCreated;

final class TodoList implements AggregateRootInterface
{
    use AggregateRootBehaviourTrait;

    private(set) AggregateRootId $aggregateRootId {
        get => $this->aggregateRootId;
        set => $value;
    }

    private(set) string $title {
        get => $this->title;
        set => $value;
    }

    private(set) ?string $description {
        get => $this->description;
        set => $value;
    }

    private(set) ?DateTime $deadLine {
        get => $this->deadLine;
        set => $value;
    }

    private(set) bool $isFinished = false {
        get => $this->isFinished;
        set => $value;
    }

    private(set) DateTime $createdAt {
        get => $this->createdAt;
        set => $value;
    }

    /**
     * @param non-falsy-string $title
     *
     * @throws InvalidAggregateStringProvidedException
     * @throws MissingMethodToApplyEventException
     * @throws InvalidUuidStringProvidedException
     * @throws DateTimeException
     */
    public static function createWithTitle(AggregateRootId $id, string $title): self
    {
        $todoList = new self();
        $todoList->apply(
            event: new TodoListWithTitleWasCreated(
                id: $id,
                title: $title,
                createdAt: DateTime::now(),
            )
        );

        return $todoList;
    }

    /**
     * @throws InvalidAggregateStringProvidedException
     * @throws MissingMethodToApplyEventException
     * @throws InvalidUuidStringProvidedException
     * @throws DateTimeException
     */
    public function setDescription(?string $description): void
    {
        $this->apply(
            event: new DescriptionWasSet(
                id: $this->aggregateRootId,
                description: $description
            ),
        );
    }

    public function applyTodoListWithTitleWasCreated(TodoListWithTitleWasCreated $event): void
    {
        $this->aggregateRootId = $event->id;
        $this->title = $event->title;
        $this->createdAt = $event->createdAt;
        $this->isFinished = false;
    }

    public function applyDescriptionWasSet(DescriptionWasSet $event): void
    {
        $this->description = $event->description;
    }

    public function getAggregateRootId(): \Stringable
    {
        return $this->aggregateRootId;
    }

    public function getId(): string
    {
        return (string) $this->getAggregateRootId();
    }
}
