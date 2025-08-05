<?php

namespace App\Domain\Shared\Specification\Error;

interface ErrorCodeInterface
{
    public function getCode(): int;

    public function getTitle(): string;
}
