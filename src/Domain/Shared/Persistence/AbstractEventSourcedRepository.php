<?php

declare(strict_types=1);

namespace App\Domain\Shared\Persistence;

use App\Domain\Shared\Aggregate\AggregateRootInterface;
use App\Domain\Shared\Event\EventBusInterface;
use App\Domain\Shared\Persistence\Write\EventStoreInterface;
use Throwable;

/**
 * @template T of AggregateRootInterface
 *
 * @template-implements EventSourcedRepositoryInterface<T>
 */
abstract readonly class AbstractEventSourcedRepository implements EventSourcedRepositoryInterface
{
    protected function __construct(
        /** @var EventStoreInterface<T> */
        protected EventStoreInterface $eventStore,
        protected EventBusInterface $eventBus,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function save(AggregateRootInterface $aggregateRoot): void
    {
        $uncommitted = $aggregateRoot->getUncommittedMessages();

        $this->eventStore->append(
            aggregate: $aggregateRoot->aggregateRootId,
            stream: $uncommitted,
        );

        foreach ($uncommitted->getMessages() as $message) {
            $this->eventBus->publish(event: $message->getPayload());
        }
    }

    public function load(\Stringable $aggregateRootId): AggregateRootInterface
    {
        return $this->eventStore->load(aggregateRootId: $aggregateRootId);
    }
}
