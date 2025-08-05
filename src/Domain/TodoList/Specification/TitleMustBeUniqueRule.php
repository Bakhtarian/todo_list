<?php

declare(strict_types=1);

namespace App\Domain\TodoList\Specification;

use App\Domain\Shared\Specification\BusinessRuleValidationMessage;
use App\Domain\Shared\Specification\Rule\BusinessRuleSpecificationInterface;
use App\Domain\Shared\ValueObject\BooleanValue;
use App\Domain\TodoList\Specification\Checker\TitleUniquenessCheckerInterface;
use App\Domain\TodoList\Specification\Error\ErrorCodes;
use App\Domain\TodoList\ValueObject\Title;

final readonly class TitleMustBeUniqueRule implements BusinessRuleSpecificationInterface
{
    public const string VALIDATION_MESSAGE = 'Each Todo List must have a unique title.';

    public function __construct(
        private TitleUniquenessCheckerInterface $titleUniquenessChecker,
        private Title $title,
    ) {
    }

    public function isSatisfiedBy(): BooleanValue
    {
        return BooleanValue::create(
            value: $this->titleUniquenessChecker->isUnique(
                title: $this->title
            )
        );
    }

    public function validationMessage(): BusinessRuleValidationMessage
    {
        return new BusinessRuleValidationMessage(
            message: self::VALIDATION_MESSAGE,
            error: ErrorCodes::TitleIsNotUniqueError,
        );
    }
}
