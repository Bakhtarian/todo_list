<?php

declare(strict_types=1);

namespace App\Tests\Todo;

use App\Application\TodoList\Command\AddDescriptionToTodoListCommand;
use App\Application\TodoList\Command\CreateTodoListWithTitleCommand;
use App\Domain\Shared\Exception\BusinessRuleValidationException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\TodoList\ValueObject\Description;
use App\Domain\TodoList\ValueObject\Title;
use App\Infrastructure\TodoList\Persistence\Read\DetailView\DetailView;
use App\Infrastructure\TodoList\Persistence\Read\Overview\Overview;
use PHPUnit\Framework\Attributes\Group;

#[Group('todo-list')]
final class TodoListTest extends BaseTodoTest
{
    private const string TITLE = 'Test list';
    private const string DESCRIPTION = 'Test description';

    public function testTheTodoListCanBeCreated(): void
    {
        $createNewListCommand = new CreateTodoListWithTitleCommand(
            aggregateRootId: AggregateRootId::create(),
            title: Title::create(value: self::TITLE),
        );

        $this->commandBus->dispatch(command: $createNewListCommand);

        $detailView = $this->detailViewRepository->findBy(fields: ['title' => self::TITLE]);
        $allDetailViewReadModels = $this->detailViewRepository->findAll();

        $overview = $this->overviewRepository->findBy(fields: ['title' => self::TITLE]);
        $allOverviewReadModels = $this->overviewRepository->findAll();

        self::assertCount(1, $allDetailViewReadModels, 'There is more than one read model created');
        self::assertCount(1, $detailView, 'There is more than one read model created');
        self::assertInstanceOf(DetailView::class, $detailView[0]);

        self::assertCount(1, $allOverviewReadModels, 'There is more than one read model created');
        self::assertCount(1, $overview, 'There is more than one read model created');
        self::assertInstanceOf(Overview::class, $overview[0]);
    }

    public function testThatCreatingTodoListFailsIfTitleIsAlreadyTaken(): void
    {
        self::expectException(BusinessRuleValidationException::class);

        $this->givenTodoListExistsWithTitle(title: self::TITLE);

        $this->commandBus->dispatch(
            command: new CreateTodoListWithTitleCommand(
                aggregateRootId: AggregateRootId::create(),
                title: Title::create(value: self::TITLE),
            )
        );
    }

    public function testTodoListCanBeUpdated(): void
    {
        $aggregateRootId = AggregateRootId::create();

        $this->givenTodoListExistsWithTitle(
            title: self::TITLE,
            withAggregate: $aggregateRootId
        );

        $overview = $this->overviewRepository->findBy(fields: ['title' => self::TITLE]);
        $allOverviewReadModels = $this->overviewRepository->findAll();
        $detailView = $this->detailViewRepository->findBy(fields: ['title' => self::TITLE]);
        $allDetailViewReadModels = $this->detailViewRepository->findAll();
        self::assertCount(1, $this->eventStore->loadAllMessages());
        self::assertCount(1, $allOverviewReadModels);
        self::assertCount(1, $allDetailViewReadModels);
        self::assertNull($overview[0]->updatedAt);
        self::assertNull($detailView[0]->description);

        $this->commandBus->dispatch(
            command: new AddDescriptionToTodoListCommand(
                aggregateRootId: $aggregateRootId,
                description: Description::create(value: self::DESCRIPTION),
            )
        );

        $overview = $this->overviewRepository->findBy(fields: ['title' => self::TITLE]);
        $detailView = $this->detailViewRepository->findBy(fields: ['title' => self::TITLE]);
        self::assertCount(2, $this->eventStore->loadAllMessages());
        self::assertCount(1, $allOverviewReadModels);
        self::assertCount(1, $allDetailViewReadModels);
        self::assertNotNull($overview[0]->updatedAt);
        self::assertNotNull($detailView[0]->description);
    }
}
