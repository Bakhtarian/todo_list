<?php

namespace App\Domain\Shared\Persistence;

use App\Domain\Shared\IdentifiableInterface;

/**
 * @template T of IdentifiableInterface
 */
interface ReadModelRepositoryInterface
{
    /**
     * @phpstan-param T $identifiableModel
     */
    public function save(IdentifiableInterface $identifiableModel): void;

    /**
     * @phpstan-param T $identifiableModel
     */
    public function update(IdentifiableInterface $identifiableModel): void;

    /**
     * @phpstan-return T|null
     */
    public function find(string $id): ?IdentifiableInterface;

    /**
     * @param array<string, mixed> $fields
     *
     * @phpstan-return array<T>
     */
    public function findBy(array $fields): array;

    /**
     * @phpstan-return array<T>
     */
    public function findAll(): array;

    /**
     * @phpstan-param T $identifiableModel
     */
    public function remove(IdentifiableInterface $identifiableModel): void;
}
