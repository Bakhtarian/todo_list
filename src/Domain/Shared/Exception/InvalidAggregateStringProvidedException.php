<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class InvalidAggregateStringProvidedException extends \Exception
{
    public const string MESSAGE = 'Aggregate string "%s" is invalid. Excepted type of Uuid::v7';

    public static function withAggregateString(string $aggregate): self
    {
        return new self(
            message: sprintf(self::MESSAGE, $aggregate)
        );
    }
}
