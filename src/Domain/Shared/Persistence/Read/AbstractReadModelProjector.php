<?php

declare(strict_types=1);

namespace App\Domain\Shared\Persistence\Read;

use App\Domain\Shared\Event\EventInterface;
use App\Domain\Shared\Exception\CouldNotFindReadModelException;
use App\Domain\Shared\Exception\MissingMethodToApplyEventException;
use App\Domain\Shared\Exception\TooManyResultsException;
use App\Domain\Shared\IdentifiableInterface;
use App\Domain\Shared\Persistence\ReadModelRepositoryInterface;
use App\Domain\Shared\Projector\ProjectorInterface;

/**
 * @template T of IdentifiableInterface
 */
abstract readonly class AbstractReadModelProjector implements ProjectorInterface
{
    /**
     * @param ReadModelRepositoryInterface<T> $repository
     */
    protected function __construct(
        protected ReadModelRepositoryInterface $repository,
    ) {
    }

    /**
     * @return T
     *
     * @throws TooManyResultsException
     * @throws CouldNotFindReadModelException
     */
    protected function getReadModel(string $id): IdentifiableInterface
    {
        $readModel = $this->repository->find(id: $id);

        if (null === $readModel) {
            throw new CouldNotFindReadModelException();
        }

        return $readModel;
    }

    /**
     * @throws MissingMethodToApplyEventException
     */
    public function __invoke(EventInterface $message): void
    {
        $eventToHandle = $message;
        $handleMethod = $this->getHandleMethod(event: $message);

        if (!method_exists($this, $handleMethod)) {
            return;
        }

        $this->{$handleMethod}(event: $eventToHandle);
    }

    private function getHandleMethod(EventInterface $event): string
    {
        $classParts = explode('\\', get_class($event));

        return 'handle' . end($classParts);
    }
}
