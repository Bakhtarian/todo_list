<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Persistence\Exception;

final class EventStoreException extends \Exception
{
    public const string MESSAGE = 'Event store error';

    public function __construct(\Throwable $previous)
    {
        parent::__construct(
            message: self::MESSAGE,
            previous: $previous
        );
    }
}
