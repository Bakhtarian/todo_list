<?php

namespace App\Domain\Shared\Query;

/**
 * @template Q of QueryInterface
 * @template T of object|null
 */
interface QueryBusInterface
{
    /**
     * @phpstan-param Q $query
     *
     * @phpstan-return T
     */
    public function ask(QueryInterface $query): ?object;
}
