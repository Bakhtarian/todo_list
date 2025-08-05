<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

use App\Domain\Shared\Specification\Rule\BusinessRuleSpecificationInterface;

final class BusinessRuleValidationException extends \Exception
{
    public function __construct(BusinessRuleSpecificationInterface $businessRuleSpecification)
    {
        parent::__construct(
            message: $businessRuleSpecification->validationMessage()->message,
            code: $businessRuleSpecification->validationMessage()->error->getCode(),
        );
    }
}
