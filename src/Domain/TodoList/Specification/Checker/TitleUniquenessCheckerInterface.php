<?php

namespace App\Domain\TodoList\Specification\Checker;

use App\Domain\TodoList\ValueObject\Title;

interface TitleUniquenessCheckerInterface
{
    public function isUnique(Title $title): bool;
}
