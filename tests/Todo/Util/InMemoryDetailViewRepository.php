<?php

declare(strict_types=1);

namespace App\Tests\Todo\Util;

use App\Domain\Shared\Exception\TooManyResultsException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\Shared\ValueObject\BooleanValue;
use App\Domain\TodoList\Persistence\Read\FindDescriptionByAggregateInterface;
use App\Domain\TodoList\Specification\Check\CheckTitleUniquenessInterface;
use App\Domain\TodoList\ValueObject\Description;
use App\Domain\TodoList\ValueObject\Title;
use App\Infrastructure\TodoList\Persistence\Read\DetailView\DetailView;
use App\Tests\Util\InMemory\InMemoryReadModelRepository;

/**
 * @template-extends InMemoryReadModelRepository<DetailView>
 */
final class InMemoryDetailViewRepository extends InMemoryReadModelRepository implements CheckTitleUniquenessInterface, FindDescriptionByAggregateInterface
{
    public function titleExists(Title $title): BooleanValue
    {
        return BooleanValue::create(
            value: !empty(
                $this->findBy(fields: ['title' => $title->value])
            )
        );
    }

    public function findDescription(AggregateRootId $aggregateRootId): ?Description
    {
        try {
            $todoList = $this->find(id: (string) $aggregateRootId);
        } catch (TooManyResultsException) {
            return null;
        }

        return $todoList?->description;
    }
}
