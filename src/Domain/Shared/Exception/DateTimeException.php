<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class DateTimeException extends \Exception
{
    public const string MESSAGE = 'Datetime Malformed or not valid';

    public function __construct(\Throwable $exception)
    {
        parent::__construct(
            message: self::MESSAGE,
            previous: $exception
        );
    }
}
