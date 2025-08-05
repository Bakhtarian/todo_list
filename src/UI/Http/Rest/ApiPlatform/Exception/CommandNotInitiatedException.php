<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Exception;

final class CommandNotInitiatedException extends \Exception
{
    public static function withData(?string $listDescription, ?string $dataDescription): self
    {
        $listDescription ??= 'is null';
        $dataDescription ??= 'is null';

        return new self(
            message: sprintf(
                'Command not initiated. Current list description: "%s", provided description: "%s"',
                $listDescription,
                $dataDescription
            )
        );
    }
}
