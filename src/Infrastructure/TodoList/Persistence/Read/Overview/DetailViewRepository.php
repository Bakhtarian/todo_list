<?php

declare(strict_types=1);

namespace App\Infrastructure\TodoList\Persistence\Read\Overview;

use App\Infrastructure\Shared\Persistence\Read\AbstractMongoDBReadModelRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

/**
 * @template-extends AbstractMongoDBReadModelRepository<DetailView>
 */
final readonly class DetailViewRepository extends AbstractMongoDBReadModelRepository
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
}
