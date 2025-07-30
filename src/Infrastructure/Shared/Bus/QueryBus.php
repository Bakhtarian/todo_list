<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Bus;

use App\Domain\Shared\Query\QueryBusInterface;
use App\Domain\Shared\Query\QueryInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Throwable;

/**
 * @template Q of QueryInterface
 * @template T of object
 *
 * @template-implements QueryBusInterface<Q, T>
 */
final readonly class QueryBus implements QueryBusInterface
{
    use MessageBusExceptionTrait;

    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    /**
     * @phpstan-return T|null
     *
     * @throws Throwable
     */
    public function ask(QueryInterface $query): ?object
    {
        $stamp = null;

        try {
            $envelope = $this->queryBus->dispatch($query);
            $stamp = $envelope->last(stampFqcn: HandledStamp::class);
        } catch (HandlerFailedException $exception) {
            $this->throwException($exception);
        }

        if (!$stamp instanceof HandledStamp) {
            return null;
        }

        /** @var T|null $queryResult */
        $queryResult = $stamp->getResult();

        return $queryResult;
    }
}
