<?php

namespace App\Domain\TodoList\Specification\Check;

use App\Domain\Shared\ValueObject\BooleanValue;
use App\Domain\TodoList\ValueObject\Title;

interface CheckTitleUniquenessInterface
{
    public function titleExists(Title $title): BooleanValue;
}
