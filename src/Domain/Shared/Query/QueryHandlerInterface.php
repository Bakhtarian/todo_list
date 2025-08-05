<?php

namespace App\Domain\Shared\Query;

/**
 * @template Q of QueryInterface
 * @template T of object|null
 */
interface QueryHandlerInterface
{
    /**
     * @phpstan-param Q $query
     *
     * @phpstan-return T
     */
    public function __invoke(QueryInterface $query): ?object;
}
