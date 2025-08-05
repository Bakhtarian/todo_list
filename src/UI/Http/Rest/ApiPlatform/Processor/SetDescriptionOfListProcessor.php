<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\TodoList\Command\AddDescriptionToTodoListCommand;
use App\Application\TodoList\Command\AdjustDescriptionOfTodoListCommand;
use App\Application\TodoList\Command\RemoveDescriptionFromTodoListCommand;
use App\Application\TodoList\Query\FindTodoListDescriptionQuery;
use App\Domain\Shared\Command\CommandBusInterface;
use App\Domain\Shared\Command\CommandInterface;
use App\Domain\Shared\Exception\ValueObjectDidNotMeetValidationException;
use App\Domain\Shared\Query\QueryBusInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\Domain\TodoList\ValueObject\Description;
use App\UI\Http\Rest\ApiPlatform\Exception\CommandNotInitiatedException;
use App\UI\Http\Rest\ApiPlatform\Exception\MissingDataException;
use App\UI\Http\Rest\ApiPlatform\Input\SetDescriptionForListInput;
use App\UI\Http\Rest\ApiPlatform\Output\TodoListIdentifier;

/**
 * @template T of SetDescriptionForListInput
 *
 * @template-implements ProcessorInterface<T, TodoListIdentifier>
 */
final readonly class SetDescriptionOfListProcessor implements ProcessorInterface
{
    /**
     * @phpstan-param QueryBusInterface<FindTodoListDescriptionQuery, Description|null> $queryBus
     */
    public function __construct(
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
    ) {
    }

    /**
     * @phpstan-param T $data
     * @phpstan-param array<string, non-empty-string> $uriVariables
     *
     * @throws MissingDataException
     * @throws ValueObjectDidNotMeetValidationException
     * @throws CommandNotInitiatedException
     */
    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ): TodoListIdentifier {
        if (!array_key_exists('id', $uriVariables)) {
            throw MissingDataException::withKeyAndType(
                missingKey: 'id',
                missingType: 'string',
                received: $uriVariables,
            );
        }

        $aggregateRootId = AggregateRootId::fromString(value: $uriVariables['id']);
        $listDescription = $this->queryBus->ask(new FindTodoListDescriptionQuery(aggregateRootId: $aggregateRootId));
        $command = null;

        if (empty($data->description) && null === $listDescription) {
            return TodoListIdentifier::create(aggregateRootId: $aggregateRootId);
        }

        if (
            null === $listDescription
            && !empty($data->description)
        ) {
            $command = new AddDescriptionToTodoListCommand(
                aggregateRootId: $aggregateRootId,
                description: Description::fromString(value: $data->description),
            );
        }

        if (
            null === $data->description
            && $listDescription instanceof Description
        ) {
            $command = new RemoveDescriptionFromTodoListCommand(aggregateRootId: $aggregateRootId);
        }

        if (
            !empty($data->description)
            && $listDescription instanceof Description
        ) {
            $command = new AdjustDescriptionOfTodoListCommand(
                aggregateRootId: $aggregateRootId,
                description: Description::fromString(value: $data->description),
            );
        }

        if (!$command instanceof CommandInterface) {
            throw CommandNotInitiatedException::withData(
                listDescription: $listDescription?->toString(),
                dataDescription: $data->description,
            );
        }

        $this->commandBus->dispatch(command: $command);

        return TodoListIdentifier::create(aggregateRootId: $aggregateRootId);
    }
}
