<?php

declare(strict_types=1);

namespace App\Tests\Util\Stub;

use App\Domain\Shared\Event\EventBusInterface;
use App\Domain\Shared\Event\EventHandlerInterface;
use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Projector\ProjectorInterface;

final class EventBusStub implements EventBusInterface
{
    /**
     * @param EventHandlerInterface<EventInterface>[] $subscribeHandlers
     * @param ProjectorInterface<EventInterface>[] $subscribeProjections
     */
    public function __construct(
        private array $subscribeHandlers = [],
        private array $subscribeProjections = [],
    ) {
    }

    public function publish(EventInterface $event): void
    {
        foreach ($this->subscribeHandlers as $subscribeHandler) {
            $subscribeHandler->handle(event: $event);
        }

        foreach ($this->subscribeProjections as $subscribeProjection) {
            $subscribeProjection->handle(message: $event);
        }
    }

    /**
     * @param EventHandlerInterface<EventInterface>  ...$eventToSubscribeTo
     */
    public function subscribe(EventHandlerInterface ...$eventToSubscribeTo): void
    {
        $this->subscribeHandlers = array_merge($this->subscribeHandlers, $eventToSubscribeTo);
    }

    /**
     * @param ProjectorInterface<EventInterface>  ...$projectors
     */
    public function subscribeProjectors(ProjectorInterface ...$projectors): void
    {
        $this->subscribeProjections = array_merge($this->subscribeProjections, $projectors);
    }
}
