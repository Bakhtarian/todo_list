<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

class DateTimeWithDatetimeIntervalFailedException extends \Exception
{
    public const string MESSAGE = 'Interval "%s" is not valid';

    public static function withInterval(string $intervalFormat): self
    {
        return new self(
            message: sprintf(self::MESSAGE, $intervalFormat)
        );
    }
}
