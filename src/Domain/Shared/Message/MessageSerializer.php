<?php

declare(strict_types=1);

namespace App\Domain\Shared\Message;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\Shared\ValueObject\DateTime;

/**
 * @phpstan-type serializedData array{
 *        uuid: non-empty-string,
 *        playhead: int,
 *        payload: non-empty-string,
 *        meta_data: non-empty-string,
 *        recorded_at: string,
 *        type: class-string<EventInterface>,
 *   }
 *
 * @template-implements MessageSerializerInterface<Message>
 */
final readonly class MessageSerializer implements MessageSerializerInterface
{
    /**
     * @phpstan-return serializedData
     */
    public function serialize(MessageInterface $message): array
    {
        $decodedPayload = json_encode(
            $message->getPayload()->serialize()
        );

        $metaData = json_encode(
            $message->getMetaData()
        );

        assert(is_string($decodedPayload));
        assert(is_string($metaData));

        return [
            'uuid' => $message->getId(),
            'playhead' => $message->getPlayhead(),
            'payload' => $decodedPayload,
            'meta_data' => $metaData,
            'recorded_at' => $message->getRecordedAt()->toString(),
            'type' => $message->getType(),
        ];
    }

    /**
     * @phpstan-param serializedData $data
     *
     * @return Message
     *
     * @throws DateTimeException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function deserialize(array $data): MessageInterface
    {
        $eventType = $data['type'];
        /** @var array<string, mixed>|false $decodedPayload */
        $decodedPayload = json_decode(
            json: $data['payload'],
            associative: true
        );

        /** @var array<string, mixed> $decodedMetaData */
        $decodedMetaData = json_decode(
            json: $data['meta_data'],
            associative: true
        );

        assert(is_array($decodedPayload));
        $payload = $eventType::deserialize(data: $decodedPayload);

        return new Message(
            aggregateRootId: AggregateRootId::fromString(value: $data['uuid']),
            playhead: $data['playhead'],
            metaData: $decodedMetaData,
            payload: $payload,
            recordedAt: DateTime::fromString(dateTime: $data['recorded_at'])
        );
    }
}
