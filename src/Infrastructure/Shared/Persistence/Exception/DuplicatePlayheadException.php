<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Persistence\Exception;

use App\Domain\Shared\Message\MessageStreamInterface;

final class DuplicatePlayheadException extends \Exception
{
    public const string MESSAGE = 'Duplicate playhead in stream found. Stream: %s';

    // @phpstan-ignore-next-line
    public function __construct(
        MessageStreamInterface $messageStream,
        \Exception $previous,
    ) {
        $playheadInStream = [];

        foreach ($messageStream->getMessages() as $stream) {
            $playheadInStream[] = $stream->getPlayhead();
        }

        parent::__construct(
            message: sprintf(self::MESSAGE, implode(', ', $playheadInStream)),
            previous: $previous,
        );
    }
}
