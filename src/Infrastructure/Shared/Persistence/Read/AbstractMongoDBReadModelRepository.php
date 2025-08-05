<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Persistence\Read;

use App\Domain\Shared\Exception\CouldNotSaveReadModelException;
use App\Domain\Shared\Exception\TooManyResultsException;
use App\Domain\Shared\IdentifiableInterface;
use App\Domain\Shared\Persistence\Read\SerializableReadModelInterface;
use App\Domain\Shared\Persistence\ReadModelRepositoryInterface;
use App\Domain\Shared\SerializerInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\Collection;

/**
 * @template T of SerializableReadModelInterface
 *
 * @template-implements ReadModelRepositoryInterface<T>
 */
abstract readonly class AbstractMongoDBReadModelRepository implements ReadModelRepositoryInterface
{
    public function __construct(
        protected DocumentManager $documentManager,
        /** @var SerializerInterface<T> */
        protected SerializerInterface $serializer,
        protected string $collectionName,
        protected string $databaseName,
        protected int $maxRetryCount = 5,
        protected string $aggregateRootColumnName = 'uuid',
    ) {
    }

    /**
     * @throws CouldNotSaveReadModelException
     */
    public function save(IdentifiableInterface $identifiableModel): void
    {
        $maxTryCount = $this->maxRetryCount;

        do {
            $isAcknowledged = $this->prepareCollectionConnection()
                ->insertOne(
                    document: $this->serializer
                        ->serialize(identifiable: $identifiableModel)
                )
                ->isAcknowledged()
            ;
        } while (!$isAcknowledged && --$maxTryCount > 0);

        if (!$isAcknowledged) {
            throw CouldNotSaveReadModelException::forModel(
                model: $this->collectionName,
                data: $this->serializer->serialize(identifiable: $identifiableModel),
            );
        }
    }

    public function update(IdentifiableInterface $identifiableModel): void
    {
        $this->prepareCollectionConnection()
            ->findOneAndReplace(
                filter: [
                    $this->aggregateRootColumnName => $identifiableModel->getId(),
                ],
                replacement: $this->serializer
                    ->serialize(identifiable: $identifiableModel),
            );
    }

    /**
     * @return T
     *
     * @throws TooManyResultsException
     */
    public function find(string $id): ?IdentifiableInterface
    {
        $result = $this->findBy(fields: [
            $this->aggregateRootColumnName => $id,
        ]);

        if (empty($result)) {
            return null;
        }

        if (count($result) > 1) {
            throw TooManyResultsException::withExpectedAndGot(
                expected: 1,
                received: count($result),
            );
        }

        return $result[0];
    }

    public function findBy(array $fields): array
    {
        /** @var array{array<string, mixed>} $results */
        $results = $this->prepareCollectionConnection()
            ->find(filter: $fields)
            ->toArray()
        ;

        $readModels = [];

        foreach ($results as $result) {
            $readModels[] = $this->serializer->deserialize(data: $result);
        }

        return $readModels;
    }

    public function findAll(): array
    {
        return $this->findBy(fields: []);
    }

    public function remove(IdentifiableInterface $identifiableModel): void
    {
        $this->prepareCollectionConnection()
            ->findOneAndDelete(filter: [
                $this->aggregateRootColumnName => $identifiableModel->getId(),
            ])
        ;
    }

    protected function prepareCollectionConnection(): Collection
    {
        return $this->documentManager
            ->getClient()
            ->getCollection(
                databaseName: $this->databaseName,
                collectionName: $this->collectionName,
            );
    }
}
