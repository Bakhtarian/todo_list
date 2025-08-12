<?php

declare(strict_types=1);

namespace App\Tests\Util\InMemory;

use App\Domain\Shared\Aggregate\AggregateRootInterface;
use App\Domain\Shared\Event\EventBusInterface;
use App\Domain\Shared\Persistence\EventSourcedRepositoryInterface;
use App\Domain\Shared\Persistence\Write\EventStoreInterface;

/**
 * @template-implements EventSourcedRepositoryInterface<AggregateRootInterface>
 */
readonly class InMemoryEventSourcedRepository implements EventSourcedRepositoryInterface
{
    public function __construct(
        private EventBusInterface $eventBus,
        /** @var EventStoreInterface<AggregateRootInterface> */
        private EventStoreInterface $eventStore = new InMemoryEventStore(),
    ) {
    }

    public function save(AggregateRootInterface $aggregateRoot): void
    {
        $uncommited = $aggregateRoot->getUncommittedMessages();

        $this->eventStore->append(
            aggregate: $aggregateRoot->aggregateRootId,
            stream: $uncommited,
        );

        foreach ($uncommited->getMessages() as $message) {
            $this->eventBus->publish(event: $message->getPayload());
        }
    }

    public function load(\Stringable $aggregateRootId): AggregateRootInterface
    {
        return $this->eventStore->load(aggregateRootId: $aggregateRootId);
    }
}
