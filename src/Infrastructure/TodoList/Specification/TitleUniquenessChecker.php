<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Specification;

use App\Domain\TodoList\Specification\Check\CheckTitleUniquenessInterface;
use App\Domain\TodoList\Specification\Checker\TitleUniquenessCheckerInterface;
use App\Domain\TodoList\ValueObject\Title;

final readonly class TitleUniquenessChecker implements TitleUniquenessCheckerInterface
{
    public function __construct(private CheckTitleUniquenessInterface $checkTitleUniqueness)
    {
    }

    public function isUnique(Title $title): bool
    {
        return !$this->checkTitleUniqueness
            ->titleExists(title: $title)
            ->value
        ;
    }
}
