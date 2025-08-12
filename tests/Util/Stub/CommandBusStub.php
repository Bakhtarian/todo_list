<?php

declare(strict_types=1);

namespace App\Tests\Util\Stub;

use App\Domain\Shared\Command\CommandBusInterface;
use App\Domain\Shared\Command\CommandHandlerInterface;
use App\Domain\Shared\Command\CommandInterface;
use App\Domain\Shared\Exception\CouldNotFindEventStreamException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;

final class CommandBusStub implements CommandBusInterface
{
    /**
     * @param CommandHandlerInterface[] $subscribeHandlers
     */
    public function __construct(private array $subscribeHandlers = [])
    {
    }

    /**
     * @throws CouldNotFindEventStreamException
     * @throws DateTimeException
     * @throws InvalidAggregateStringProvidedException
     * @throws InvalidUuidStringProvidedException
     * @throws MissingMethodToApplyEventException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function dispatch(CommandInterface $command): void
    {
        foreach ($this->subscribeHandlers as $subscribeHandler) {
            $subscribeHandler->handle($command);
        }
    }

    public function subscribe(CommandHandlerInterface ...$commandToSubscribeTo): void
    {
        $this->subscribeHandlers = array_merge($this->subscribeHandlers, $commandToSubscribeTo);
    }
}
