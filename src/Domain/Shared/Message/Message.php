<?php

declare(strict_types=1);

namespace App\Domain\Shared\Message;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\Shared\ValueObject\DateTime;

/**
 * @template-implements MessageInterface<EventInterface>
 */
final readonly class Message implements MessageInterface
{
    /**
     * @param array<string, mixed> $metaData
     *
     * @phpstan-param EventInterface $payload
     */
    public function __construct(
        public AggregateRootId $aggregateRootId,
        public int $playhead,
        public array $metaData,
        public EventInterface $payload,
        public DateTime $recordedAt,
    ) {
    }

    public function getId(): string
    {
        return $this->aggregateRootId->toString();
    }

    public function getPlayhead(): int
    {
        return $this->playhead;
    }

    public function getMetaData(): array
    {
        return $this->metaData;
    }

    public function getPayload(): EventInterface
    {
        return $this->payload;
    }

    public function getRecordedAt(): DateTime
    {
        return $this->recordedAt;
    }

    public function getType(): string
    {
        return $this->payload::class;
    }

    /**
     * @param array<string, mixed> $metaData
     *
     * @throws DateTimeException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public static function recordNow(
        \Stringable $aggregateId,
        int $playhead,
        array $metaData,
        EventInterface $payload,
    ): self {
        $aggregateRootId = (string) $aggregateId;

        assert(!empty($aggregateRootId));

        return new self(
            aggregateRootId: $aggregateId instanceof AggregateRootId
                ? $aggregateId
                : AggregateRootId::fromString(value: $aggregateRootId),
            playhead: $playhead,
            metaData: $metaData,
            payload: $payload,
            recordedAt: DateTime::now(),
        );
    }
}
