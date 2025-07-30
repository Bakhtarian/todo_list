<?php

namespace App\Domain\Shared;

/**
 * @phpstan-type serializableData array<string, mixed>
 *
 * @template T of IdentifiableInterface
 */
interface SerializerInterface
{
    /**
     * @phpstan-param T $identifiable
     *
     * @return serializableData
     */
    public function serialize(IdentifiableInterface $identifiable): array;

    /**
     * @param serializableData $data
     *
     * @phpstan-return T
     */
    public function deserialize(array $data): IdentifiableInterface;
}
