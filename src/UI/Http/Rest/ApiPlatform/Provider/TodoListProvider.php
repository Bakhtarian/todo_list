<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Shared\Exception\CouldNotFindReadModelException;
use App\Domain\Shared\Exception\TooManyResultsException;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Infrastructure\TodoList\Persistence\Read\DetailView\DetailView;
use App\Infrastructure\TodoList\Persistence\Read\DetailView\DetailViewRepository;
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
     * @param array<string, non-empty-string> $uriVariables
     *
     * @throws MissingDataException
     * @throws TooManyResultsException
     * @throws CouldNotFindReadModelException
     * @throws ValueObjectDidNotMeetValidationException
     */
    public function provide(
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): TodoList {
        if (
            !array_key_exists('id', $uriVariables)
        ) {
            throw MissingDataException::withKeyAndType(
                missingKey: 'id',
                missingType: 'string',
                received: $uriVariables,
            );
        }

        $aggregateRootId = AggregateRootId::fromString(value: $uriVariables['id']);
        $detailViewReadModel = $this->repository->find(id: (string) $aggregateRootId);

        if (!$detailViewReadModel instanceof DetailView) {
            throw new CouldNotFindReadModelException();
        }

        return TodoList::create(
            detailView: $detailViewReadModel,
        );
    }
}
