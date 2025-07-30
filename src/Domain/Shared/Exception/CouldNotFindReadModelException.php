<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class CouldNotFindReadModelException extends \Exception
{
    public const string MESSAGE = 'Could not find read model.';

    public function __construct()
    {
        parent::__construct(message: self::MESSAGE);
    }
}
