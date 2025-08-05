<?php

namespace App\Domain\Shared\Specification\Rule;

use App\Domain\Shared\Specification\BusinessRuleValidationMessage;
use App\Domain\Shared\ValueObject\BooleanValue;

interface BusinessRuleSpecificationInterface
{
    public function isSatisfiedBy(): BooleanValue;

    public function validationMessage(): BusinessRuleValidationMessage;
}
