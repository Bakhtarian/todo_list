<?php

declare(strict_types=1);

namespace App\Domain\Shared\Message;

/**
 * @template T of MessageInterface
 *
 * @template-implements MessageStreamInterface<T>
 */
final readonly class MessageStream implements MessageStreamInterface
{
    /**
     * @param array<T> $messages
     */
    public function __construct(
        private array $messages,
    ) {
    }

    /**
     * @phpstan-return \Generator<T>
     */
    public function getMessages(): \Generator
    {
        yield from $this->messages;
    }
}
