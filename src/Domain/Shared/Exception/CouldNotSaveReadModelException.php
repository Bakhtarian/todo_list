<?php

declare(strict_types=1);

namespace App\Domain\Shared\Exception;

final class CouldNotSaveReadModelException extends \Exception
{
    public const string MESSAGE = 'Could not save read model "%s" with data "%s"';

    /**
     * @param array<string, mixed> $data
     */
    public static function forModel(string $model, array $data): self
    {
        return new self(
            message: sprintf(self::MESSAGE, $model, json_encode($data))
        );
    }
}
