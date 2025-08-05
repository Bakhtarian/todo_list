<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\Overview;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\IdentifiableInterface;
use App\Domain\Shared\SerializerInterface;

/**
 * @phpstan-import-type overviewData from Overview
 *
 * @template-implements SerializerInterface<Overview>
 */
final readonly class OverviewSerializer implements SerializerInterface
{
    public function serialize(IdentifiableInterface $identifiable): array
    {
        return $identifiable->serialize();
    }

    /**
     * @param overviewData $data
     *
     * @throws DateTimeException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function deserialize(array $data): IdentifiableInterface
    {
        return Overview::deserialize(data: $data);
    }
}
