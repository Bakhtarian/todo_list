<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Application\TodoList\Command\CreateTodoListWithTitleCommand;
use App\Domain\Shared\Command\CommandBusInterface;
use App\Domain\Shared\ValueObject\AggregateRootId;
use App\UI\Http\Rest\ApiPlatform\Input\TodoListCreationInput;
use App\UI\Http\Rest\ApiPlatform\Output\TodoListIdentifier;

/**
 * @template-implements ProcessorInterface<TodoListCreationInput, TodoListIdentifier>
 */
final readonly class CreateNewListProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = [],
    ) {
        $aggregateRoot = AggregateRootId::create();

        $this->commandBus->dispatch(
            command: new CreateTodoListWithTitleCommand(
                id: $aggregateRoot,
                title: $data->title,
            )
        );

        return TodoListIdentifier::create(aggregateRootId: $aggregateRoot);
    }
}
