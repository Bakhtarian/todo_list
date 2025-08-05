<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\DetailView;

use App\Domain\Shared\Exception\TooManyResultsException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\Shared\ValueObject\BooleanValue;
use App\Domain\TodoList\Persistence\Read\FindDescriptionByAggregateInterface;
use App\Domain\TodoList\Specification\Check\CheckTitleUniquenessInterface;
use App\Domain\TodoList\ValueObject\Description;
use App\Domain\TodoList\ValueObject\Title;
use App\Infrastructure\Shared\Persistence\Read\AbstractMongoDBReadModelRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * @template-extends AbstractMongoDBReadModelRepository<DetailView>
 */
final readonly class DetailViewRepository extends AbstractMongoDBReadModelRepository implements CheckTitleUniquenessInterface, FindDescriptionByAggregateInterface
{
    private const string COLLECTION_NAME = 'detail-view';
    private const string DATABASE_NAME = 'read_models';

    public function __construct(DocumentManager $documentManager)
    {
        parent::__construct(
            documentManager: $documentManager,
            serializer: new DetailViewSerializer(),
            collectionName: self::COLLECTION_NAME,
            databaseName: self::DATABASE_NAME,
        );
    }

    public function titleExists(Title $title): BooleanValue
    {
        return BooleanValue::create(
            value: !empty(
                $this->findBy(fields: ['title' => (string) $title])
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
