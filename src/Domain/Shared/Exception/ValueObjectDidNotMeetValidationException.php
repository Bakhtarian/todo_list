<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class ValueObjectDidNotMeetValidationException extends \Exception
{
    public static function withMessage(?string $message = null): self
    {
        $exceptionMessage = 'Validation failed on value object.';

        if ($message !== null) {
            $exceptionMessage .= sprintf(' Due to: "%s".', $message);
        }

        return new self(message: $exceptionMessage);
    }
}
