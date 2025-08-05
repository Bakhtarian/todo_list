<?php

namespace App\Domain\Shared\ValueObject;

/**
 * @template T
 */
interface ValueObjectInterface
{
    public function getValue(): mixed;
}
