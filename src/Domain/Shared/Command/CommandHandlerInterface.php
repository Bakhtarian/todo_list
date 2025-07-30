<?php

namespace App\Domain\Shared\Command;

use App\Domain\Shared\Exception\CouldNotFindEventStreamException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;

/**
 * @template T of CommandInterface
 */
interface CommandHandlerInterface
{
    /**
     * @phpstan-param T $command
     *
     * @throws InvalidAggregateStringProvidedException
     * @throws InvalidUuidStringProvidedException
     * @throws InvalidAggregateStringProvidedException
     * @throws CouldNotFindEventStreamException
     * @throws MissingMethodToApplyEventException
     * @throws InvalidUuidStringProvidedException
     * @throws DateTimeException
     */
    public function handle(CommandInterface $command): void
    ;
}
