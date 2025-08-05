<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

final readonly class BooleanValue
{
    private function __construct(public bool $value)
    {
    }

    public static function create(bool $value): self
    {
        return new self(value: $value);
    }

    public function isTrue(): bool
    {
        return $this->value === true;
    }

    public function isFalse(): bool
    {
        return $this->value === false;
    }

    public function isValid(): bool
    {
        return $this->value === true;
    }

    public function isInvalid(): bool
    {
        return $this->value === false;
    }
}
