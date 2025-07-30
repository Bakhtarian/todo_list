<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\TodoList\Command\SetDescriptionToTodoListCommand;
use App\Domain\Shared\Command\CommandBusInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;
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
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    /**
     * @phpstan-param T $data
     * @phpstan-param array<string, string> $uriVariables
     *
     * @throws MissingDataException
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

        $this->commandBus->dispatch(
            command: new SetDescriptionToTodoListCommand(
                id: $uriVariables['id'],
                description: $data->description,
            )
        );

        return TodoListIdentifier::create(aggregateRootId: AggregateRootId::fromString(uuid: $uriVariables['id']));
    }
}
