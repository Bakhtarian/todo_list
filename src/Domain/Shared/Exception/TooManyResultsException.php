<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class TooManyResultsException extends \Exception
{
    public const string MESSAGE = 'Too many results. Expected "%s" but got "%s"';

    public static function withExpectedAndGot(int $expected, int $received): self
    {
        return new self(
            message: sprintf(self::MESSAGE, $expected, $received)
        );
    }
}
