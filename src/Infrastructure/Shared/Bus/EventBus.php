<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Bus;

use App\Domain\Shared\Event\EventBusInterface;
use App\Domain\Shared\Event\EventInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final readonly class EventBus implements EventBusInterface
{
    use MessageBusExceptionTrait;

    public function __construct(private MessageBusInterface $eventBus)
    {
    }

    /**
     * @throws Throwable
     */
    public function publish(EventInterface $event): void
    {
        try {
            $this->eventBus->dispatch($event);
        } catch (HandlerFailedException $exception) {
            $this->throwException($exception);
        }
    }
}
