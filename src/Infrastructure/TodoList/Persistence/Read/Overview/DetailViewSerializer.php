<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\Overview;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\IdentifiableInterface;
use App\Domain\Shared\SerializerInterface;

/**
 * @phpstan-import-type detailViewData from DetailView
 *
 * @template-implements SerializerInterface<DetailView>
 */
final readonly class DetailViewSerializer implements SerializerInterface
{
    public function serialize(IdentifiableInterface $identifiable): array
    {
        return $identifiable->serialize();
    }

    /**
     * @param detailViewData $data
     *
     * @throws DateTimeException
     */
    public function deserialize(array $data): DetailView
    {
        return DetailView::deserialize(data: $data);
    }
}
