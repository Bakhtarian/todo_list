<?php

declare(strict_types=1);

namespace App\Domain\Shared\Replay;

use App\Domain\Shared\Aggregate\AggregateRootInterface;
use App\Domain\Shared\Event\EventBusInterface;
use App\Domain\Shared\Event\EventCriteriaInterface;
use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Message\MessageInterface;
use App\Domain\Shared\Persistence\Write\EventStoreInterface;

/**
 * @template T of AggregateRootInterface
 */
abstract class Replay implements ReplayInterface
{
    /**
     * @template T of EventCriteriaInterface
     *
     * @phpstan-var EventCriteriaInterface<T>|null
     */
    private ?EventCriteriaInterface $criteria = null;

    protected function __construct(
        /** @var EventStoreInterface<T> */
        private readonly EventStoreInterface $eventStore,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function withCriteria(EventCriteriaInterface $criteria): static
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function replay(): void
    {
        $messagesToReplay = $this->eventStore->loadAll();

        if ($this->criteria !== null) {
            foreach ($messagesToReplay as $index => $message) {
                if (!$this->criteria->isMatched(event: $message->getPayload())) {
                    unset($messagesToReplay[$index]);
                }
            }
        }

        foreach ($messagesToReplay as $message) {
            $this->eventBus->publish(event: $message->getPayload());
        }
    }
}
