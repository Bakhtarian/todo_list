<?php

declare(strict_types=1);

namespace App\Domain\TodoList;

use App\Domain\Shared\Aggregate\AggregateRootBehaviourTrait;
use App\Domain\Shared\Aggregate\AggregateRootInterface;
use App\Domain\Shared\Exception\BusinessRuleValidationException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\Shared\ValueObject\DateTime;
use App\Domain\TodoList\Event\DescriptionForToDoListWasAdded;
use App\Domain\TodoList\Event\DescriptionForTodoListWasAdjusted;
use App\Domain\TodoList\Event\DescriptionForTodoListWasRemoved;
use App\Domain\TodoList\Event\TodoListWithTitleWasCreated;
use App\Domain\TodoList\Specification\Checker\TitleUniquenessCheckerInterface;
use App\Domain\TodoList\Specification\TitleMustBeUniqueRule;
use App\Domain\TodoList\ValueObject\Description;
use App\Domain\TodoList\ValueObject\Title;

final class TodoList implements AggregateRootInterface
{
    use AggregateRootBehaviourTrait;

    private(set) AggregateRootId $aggregateRootId {
        get => $this->aggregateRootId;
        set => $value;
    }

    private(set) Title $title {
        get => $this->title;
        set => $value;
    }

    private(set) ?Description $description {
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

    private(set) DateTime $updatedAt {
        get => $this->updatedAt;
        set => $value;
    }

    /**
     * @throws MissingMethodToApplyEventException
     * @throws DateTimeException
     * @throws ValueObjectDidNotMeetValidationException
     * @throws BusinessRuleValidationException
     */
    public static function createWithTitle(
        AggregateRootId $id,
        Title $title,
        TitleUniquenessCheckerInterface $titleUniquenessChecker,
    ): self {
        self::checkRule(
            businessRuleSpecification: new TitleMustBeUniqueRule(
                titleUniquenessChecker: $titleUniquenessChecker,
                title: $title,
            )
        );

        $todoList = new self();
        $todoList->apply(
            event: new TodoListWithTitleWasCreated(
                aggregateRootId: $id,
                title: $title,
                createdAt: DateTime::now(),
            )
        );

        return $todoList;
    }

    /**
     * @throws MissingMethodToApplyEventException
     * @throws DateTimeException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function addDescription(Description $description): void
    {
        $this->apply(
            event: new DescriptionForToDoListWasAdded(
                aggregateRootId: $this->aggregateRootId,
                description: $description,
                updatedAt: DateTime::now(),
            ),
        );
    }

    /**
     * @throws DateTimeException
     * @throws MissingMethodToApplyEventException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function removeDescription(): void
    {
        $this->apply(
            event: new DescriptionForTodoListWasRemoved(
                aggregateRootId: $this->aggregateRootId,
                updatedAt: DateTime::now(),
            )
        );
    }

    /**
     * @throws DateTimeException
     * @throws MissingMethodToApplyEventException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function adjustDescription(Description $description): void
    {
        $this->apply(
            event: new DescriptionForTodoListWasAdjusted(
                aggregateRootId: $this->aggregateRootId,
                description: $description,
                updatedAt: DateTime::now(),
            )
        );
    }

    public function applyTodoListWithTitleWasCreated(TodoListWithTitleWasCreated $event): void
    {
        $this->aggregateRootId = $event->aggregateRootId;
        $this->title = $event->title;
        $this->createdAt = $event->createdAt;
        $this->isFinished = false;
    }

    public function applyDescriptionForToDoListWasAdded(DescriptionForToDoListWasAdded $event): void
    {
        $this->description = $event->description;
        $this->updatedAt = $event->updatedAt;
    }

    public function applyDescriptionForTodoListWasRemoved(DescriptionForTodoListWasRemoved $event): void
    {
        $this->description = null;
        $this->updatedAt = $event->updatedAt;
    }

    public function applyDescriptionForTodoListWasAdjusted(DescriptionForTodoListWasAdjusted $event): void
    {
        $this->description = $event->description;
        $this->updatedAt = $event->updatedAt;
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
