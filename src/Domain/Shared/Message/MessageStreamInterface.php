<?php

namespace App\Domain\Shared\Message;

/**
 * @template T of MessageInterface
 */
interface MessageStreamInterface
{
    /**
     * @phpstan-return \Generator<T>
     */
    public function getMessages(): \Generator;
}
