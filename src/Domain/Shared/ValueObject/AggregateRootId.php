<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use Symfony\Component\Uid\TimeBasedUidInterface;
use Symfony\Component\Uid\Uuid;

final readonly class AggregateRootId implements \Stringable
{
    private function __construct(public TimeBasedUidInterface $uuid)
    {
    }

    public static function create(): self
    {
        return new self(Uuid::v7());
    }

    /**
     * @throws InvalidUuidStringProvidedException
     * @throws InvalidAggregateStringProvidedException
     */
    public static function fromString(string $uuid): self
    {
        if (!Uuid::isValid(uuid: $uuid)) {
            throw InvalidUuidStringProvidedException::withString(uuid: $uuid);
        }

        $aggregate = Uuid::fromString(uuid: $uuid);

        if (!$aggregate instanceof TimeBasedUidInterface) {
            throw InvalidAggregateStringProvidedException::withAggregateString(aggregate: $uuid);
        }

        return new self(uuid: $aggregate);
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        assert($this->uuid instanceof Uuid);

        return $this->uuid->toString();
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return (string) $this;
    }
}
