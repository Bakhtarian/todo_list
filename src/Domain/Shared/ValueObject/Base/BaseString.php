<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject\Base;

use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\BooleanValue;
use App\Domain\Shared\ValueObject\ValueObjectInterface;

/**
 * @template T
 *
 * @template-implements ValueObjectInterface<T>
 */
abstract readonly class BaseString implements ValueObjectInterface, \Stringable
{
    /**
     * @param non-empty-string $value
     *
     * @throws ValueObjectDidNotMeetValidationException
     */
    protected function __construct(public string $value)
    {
        $this->validate($value);
    }

    /**
     * @param non-empty-string|null $value
     *
     * @return T
     *
     * @throws ValueObjectDidNotMeetValidationException
     */
    abstract public static function create(?string $value = null);

    /**
     * @param non-empty-string $value
     *
     * @return T
     *
     * @throws ValueObjectDidNotMeetValidationException
     */
    abstract public static function fromString(string $value);

    /**
     * @param non-empty-string|null $value
     *
     * @return T|null
     *
     * @throws ValueObjectDidNotMeetValidationException
     */
    abstract public static function tryFromString(?string $value);

    /**
     * @param non-empty-string $value
     *
     * @throws ValueObjectDidNotMeetValidationException
     */
    abstract protected function validate(string $value): void;

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * @param BaseString<T> $other
     */
    public function equals(self $other): BooleanValue
    {
        return BooleanValue::create(value: $this->value === $other->value);
    }
}
