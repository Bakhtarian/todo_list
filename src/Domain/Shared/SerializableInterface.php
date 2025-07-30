<?php

namespace App\Domain\Shared;

/**
 * @template T
 */
interface SerializableInterface
{
    /**
     * @return array<string, mixed>
     */
    public function serialize(): array;

    /**
     * @param array<string, mixed> $data
     *`
     *
     * @phpstan-return T
     */
    public static function deserialize(array $data);
}
