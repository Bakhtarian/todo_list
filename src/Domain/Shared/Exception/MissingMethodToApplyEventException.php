<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class MissingMethodToApplyEventException extends \Exception
{
    public const string MESSAGE = 'Can not apply event "%s", pleas implement apply%s() method';

    public static function forEvent(string $event): self
    {
        return new self(
            message: sprintf(self::MESSAGE, $event, ucfirst($event))
        );
    }
}
