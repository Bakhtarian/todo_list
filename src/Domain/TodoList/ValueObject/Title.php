<?php

declare(strict_types=1);

namespace App\Domain\TodoList\ValueObject;

use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\Base\BaseString;

/**
 * @template-extends BaseString<Title>
 */
final readonly class Title extends BaseString
{
    public static function create(?string $value = null): Title
    {
        return new self(value: $value ?? 'null');
    }

    public static function fromString(string $value)
    {
        return new self(value: $value);
    }

    public static function tryFromString(?string $value): ?Title
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

    protected function validate(string $value): void
    {
        if (empty($value) || 'null' === $value) {
            throw ValueObjectDidNotMeetValidationException::withMessage(message: 'Title cannot be empty');
        }
    }
}
