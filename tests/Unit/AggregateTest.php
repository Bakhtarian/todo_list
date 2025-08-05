<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Application\TodoList\Command\CreateTodoListWithTitleCommand;
use App\Domain\Shared\Command\CommandBusInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Infrastructure\Shared\Bus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

final class AggregateTest extends WebTestCase
{
    private CommandBusInterface $commandBus;
    private InMemoryTransport $transport;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = self::getContainer();
        /** @var CommandBusInterface $commandBus */
        $commandBus = $container->get(CommandBus::class);
        /** @var InMemoryTransport $transport */
        $transport = $container->get('messenger.transport.async');

        $this->transport = $transport;
        $this->commandBus = $commandBus;
    }

    public function testThatTodoListWillBeInitiatedAndSetupWhenCommandHandlerCallsUponTheNamedConstructor(): void
    {
        $aggregateRootId = AggregateRootId::create();

        $this->commandBus->dispatch(
            command: new CreateTodoListWithTitleCommand(
                aggregateRootId: $aggregateRootId,
                title: 'Test',
            )
        );

        $this->assertCount(1, $this->transport->getSent());
    }
}
