<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Exception;

final class MissingDataException extends \Exception
{
    public const string MESSAGE = 'Missing data with key: "%s" of type %s. Received: %s.';

    /**
     * @phpstan-param array<string, string> $received
     */
    public static function withKeyAndType(
        string $missingKey,
        string $missingType,
        array $received,
    ): self {
        return new self(
            sprintf(
                self::MESSAGE,
                $missingKey,
                $missingType,
                implode(', ', $received)
            )
        );
    }
}
