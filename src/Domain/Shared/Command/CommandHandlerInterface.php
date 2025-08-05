<?php

namespace App\Domain\Shared\Command;

use App\Domain\Shared\Exception\CouldNotFindEventStreamException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;

interface CommandHandlerInterface
{
    /**
     * @throws InvalidAggregateStringProvidedException
     * @throws InvalidUuidStringProvidedException
     * @throws InvalidAggregateStringProvidedException
     * @throws CouldNotFindEventStreamException
     * @throws MissingMethodToApplyEventException
     * @throws InvalidUuidStringProvidedException
     * @throws DateTimeException
     * @throws DateTimeException
     * @throws MissingMethodToApplyEventException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function handle(CommandInterface $command): void
    ;
}
