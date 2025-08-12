<?php

declare(strict_types=1);

namespace App\Tests\Todo;

use App\Application\TodoList\Command\CreateTodoListWithTitleCommand;
use App\Domain\Shared\Persistence\EventSourcedRepositoryInterface;
use App\Domain\Shared\Persistence\Write\EventStoreInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\TodoList\Event\TodoListEventStoreInterface;
use App\Domain\TodoList\ValueObject\Title;
use App\Infrastructure\Shared\Bus\CommandBus;
use App\Infrastructure\TodoList\Persistence\Read\DetailView\DetailViewRepository;
use App\Infrastructure\TodoList\Persistence\Read\Overview\OverviewRepository;
use App\Infrastructure\TodoList\Persistence\TodoListRepository;
use App\Tests\Util\InMemory\InMemoryReadModelRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class BaseTodoTest extends KernelTestCase
{
    protected CommandBus $commandBus;
    protected EventStoreInterface $eventStore;
    protected EventSourcedRepositoryInterface $repository;
    protected InMemoryReadModelRepository $overviewRepository;
    protected InMemoryReadModelRepository $detailViewRepository;

    protected function setUp(): void
    {
        $this->commandBus = self::getContainer()->get(CommandBus::class);
        $this->eventStore = self::getContainer()->get(TodoListEventStoreInterface::class);
        $this->repository = self::getContainer()->get(TodoListRepository::class);
        $this->overviewRepository = self::getContainer()->get(OverviewRepository::class);
        $this->detailViewRepository = self::getContainer()->get(DetailViewRepository::class);
    }

    protected function givenTodoListExistsWithTitle(string $title, ?AggregateRootId $withAggregate = null): void
    {
        $withAggregate ??= AggregateRootId::create();

        $this->commandBus->dispatch(
            command: new CreateTodoListWithTitleCommand(
                aggregateRootId: $withAggregate,
                title: Title::create(value: $title),
            )
        );
    }
}
