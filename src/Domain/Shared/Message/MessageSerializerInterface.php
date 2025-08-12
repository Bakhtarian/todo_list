<?php

namespace App\Domain\Shared\Message;

use App\Domain\Shared\Event\EventInterface;

/**
 * @phpstan-type messageSerializerData array<string, mixed>
 *
 * @template T of MessageInterface<EventInterface>
 */
interface MessageSerializerInterface
{
    /**
     * @phpstan-param T $message
     *
     * @phpstan-return messageSerializerData
     */
    public function serialize(MessageInterface $message): array;

    /**
     * @param messageSerializerData $data
     *
     * @phpstan-return T
     */
    public function deserialize(array $data): MessageInterface;
}
