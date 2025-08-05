<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\Base\BaseString;
use Symfony\Component\Uid\TimeBasedUidInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @template-extends BaseString<AggregateRootId>
 */
final readonly class AggregateRootId extends BaseString
{
    private function __construct(string $value)
    {
        parent::__construct(value: $value);
    }

    /**
     * @throws ValueObjectDidNotMeetValidationException
     */
    public static function fromString(string $value): self
    {
        return new self(value: $value);
    }

    public static function tryFromString(?string $value): ?AggregateRootId
    {
        if (null === $value) {
            return null;
        }

        try {
            return self::fromString(value: $value);
        } catch (ValueObjectDidNotMeetValidationException) {
            return null;
        }
    }

    /**
     * @throws ValueObjectDidNotMeetValidationException
     */
    public static function create(?string $value = null): self
    {
        if (null !== $value) {
            return self::fromString(value: $value);
        }

        $uuid = Uuid::v7();

        return new self(value: $uuid->toString());
    }

    protected function validate(string $value): void
    {
        try {
            if (!Uuid::isValid(uuid: $value)) {
                throw InvalidUuidStringProvidedException::withString(uuid: $value);
            }

            $aggregate = Uuid::fromString(uuid: $value);

            if (!$aggregate instanceof TimeBasedUidInterface) {
                throw InvalidAggregateStringProvidedException::withAggregateString(aggregate: $value);
            }
        } catch (InvalidUuidStringProvidedException | InvalidAggregateStringProvidedException $e) {
            throw ValueObjectDidNotMeetValidationException::withMessage(message: $e->getMessage());
        }
    }
}
