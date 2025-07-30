<?php

namespace App\Domain\Shared\Persistence;

use App\Domain\Shared\Aggregate\AggregateRootInterface;
use App\Domain\Shared\Exception\CouldNotFindEventStreamException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;

/**
 * @template T of AggregateRootInterface
 */
interface EventSourcedRepositoryInterface
{
    /**
     * @phpstan-param T $aggregateRoot
     */
    public function save(AggregateRootInterface $aggregateRoot): void;

    /**
     * @phpstan-return T
     *
     * @throws InvalidAggregateStringProvidedException
     * @throws CouldNotFindEventStreamException
     * @throws MissingMethodToApplyEventException
     * @throws InvalidUuidStringProvidedException
     * @throws DateTimeException
     */
    public function load(\Stringable $aggregateRootId): AggregateRootInterface;
}
