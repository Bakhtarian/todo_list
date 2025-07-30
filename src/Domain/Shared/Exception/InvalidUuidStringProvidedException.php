<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class InvalidUuidStringProvidedException extends \Exception
{
    public const string MESSAGE = 'Uuid string "%s" is invalid';

    public static function withString(string $uuid): self
    {
        return new self(
            message: sprintf(self::MESSAGE, $uuid)
        );
    }
}
