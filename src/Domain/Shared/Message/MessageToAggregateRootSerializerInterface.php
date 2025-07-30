<?php

namespace App\Domain\Shared\Message;

use App\Domain\Shared\Aggregate\AggregateRootInterface;
use App\Domain\Shared\Event\EventInterface;

/**
 * @phpstan-type messageSerializerData array<string, mixed>
 *
 * @template T of AggregateRootInterface
 */
interface MessageToAggregateRootSerializerInterface
{
    /**
     * @param MessageInterface<EventInterface> $message
     *
     * @return messageSerializerData
     */
    public function serialize(MessageInterface $message): array;

    /**
     * @param messageSerializerData[] $data
     *
     * @phpstan-return T
     */
    public function deserialize(array $data): AggregateRootInterface;

    /**
     * @phpstan-param MessageSerializerInterface<MessageInterface<EventInterface>> $serializer
     *
     * @phpstan-return self<T>
     */
    public function withSerializer(MessageSerializerInterface $serializer): self;
}
