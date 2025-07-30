<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Shared\Exception\CouldNotFindReadModelException;
use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\InvalidAggregateStringProvidedException;
use App\Domain\Shared\Exception\InvalidUuidStringProvidedException;
use App\Domain\Shared\Exception\TooManyResultsException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Infrastructure\TodoList\Persistence\Read\Overview\DetailView;
use App\Infrastructure\TodoList\Persistence\Read\Overview\DetailViewRepository;
use App\UI\Http\Rest\ApiPlatform\Exception\MissingDataException;
use App\UI\Http\Rest\ApiPlatform\Output\TodoList;

/**
 * @template-implements ProviderInterface<TodoList>
 */
final readonly class TodoListProvider implements ProviderInterface
{
    public function __construct(private DetailViewRepository $repository)
    {
    }

    /**
     * @throws InvalidAggregateStringProvidedException
     * @throws MissingDataException
     * @throws InvalidUuidStringProvidedException
     * @throws DateTimeException
     * @throws TooManyResultsException
     * @throws CouldNotFindReadModelException
     */
    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): TodoList {
        if (
            !array_key_exists('id', $uriVariables)
            || !is_string($uriVariables['id'])
        ) {
            throw MissingDataException::withKeyAndType(
                missingKey: 'id',
                missingType: 'string',
                received: $uriVariables,
            );
        }

        $aggregateRootId = AggregateRootId::fromString(uuid: $uriVariables['id']);
        $detailViewReadModel = $this->repository->find(id: (string) $aggregateRootId);

        if (!$detailViewReadModel instanceof DetailView) {
            throw new CouldNotFindReadModelException();
        }

        return TodoList::create(
            detailView: $detailViewReadModel,
        );
    }
}
