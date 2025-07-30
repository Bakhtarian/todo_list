<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class CouldNotFindEventStreamException extends \Exception
{
    public const string MESSAGE = 'Could not find model "%s", with aggregate root id "%s".';

    public static function forModel(
        string $modelFQCN,
        string $aggregateRootId,
    ): self {
        return new self(
            message: sprintf(self::MESSAGE, $modelFQCN, $aggregateRootId)
        );
    }
}
