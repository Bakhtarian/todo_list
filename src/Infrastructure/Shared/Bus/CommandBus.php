<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Bus;

use App\Domain\Shared\Command\CommandBusInterface;
use App\Domain\Shared\Command\CommandInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

final readonly class CommandBus implements CommandBusInterface
{
    use MessageBusExceptionTrait;

    public function __construct(private MessageBusInterface $commandBus)
    {
    }

    /**
     * @throws Throwable
     */
    public function dispatch(CommandInterface $command): void
    {
        try {
            $this->commandBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            $this->throwException(exception: $exception);
        }
    }
}
