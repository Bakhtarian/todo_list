<?php

namespace App\Domain\Shared\Message;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\ValueObject\DateTime;

/**
 * @template T of EventInterface
 */
interface MessageInterface
{
    /**
     * @return non-empty-string
     */
    public function getId(): string;

    public function getPlayhead(): int;

    /**
     * @return array<string, mixed>
     */
    public function getMetaData(): array;

    public function getPayload(): EventInterface;

    public function getRecordedAt(): DateTime;

    /**
     * @return class-string<T>
     */
    public function getType(): string;

    /**
     * @param array<string, mixed> $metaData
     *
     * @phpstan-return MessageInterface<T>
     */
    public static function recordNow(
        \Stringable $aggregateId,
        int $playhead,
        array $metaData,
        EventInterface $payload,
    ): self;
}
