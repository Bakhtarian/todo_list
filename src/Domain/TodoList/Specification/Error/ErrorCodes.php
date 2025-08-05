<?php

namespace App\Domain\TodoList\Specification\Error;

use App\Domain\Shared\Specification\Error\ErrorCodeInterface;

enum ErrorCodes: int implements ErrorCodeInterface
{
    case TitleIsNotUniqueError = 101;

    public function getCode(): int
    {
        return $this->value;
    }

    public function getTitle(): string
    {
        return $this->name;
    }
}
