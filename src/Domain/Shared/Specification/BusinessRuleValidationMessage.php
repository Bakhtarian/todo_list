<?php

declare(strict_types=1);

namespace App\Domain\Shared\Specification;

use App\Domain\Shared\Specification\Error\ErrorCodeInterface;

final readonly class BusinessRuleValidationMessage
{
    public function __construct(
        public string $message,
        public ErrorCodeInterface $error,
    ) {
    }
}
