<?php

declare(strict_types=1);

namespace App\Tests\Util\Stub;

use App\Domain\Shared\IdentifiableInterface;
use App\Domain\Shared\Persistence\Read\AbstractReadModelProjector;
use App\Domain\Shared\Persistence\ReadModelRepositoryInterface;
use App\Tests\Util\InMemory\InMemoryReadModelRepository;

/**
 * @template I of IdentifiableInterface
 * @template T of ReadModelRepositoryInterface<I>
 *
 * @template-extends AbstractReadModelProjector<I, T>
 */
abstract readonly class AbstractProjectorStub extends AbstractReadModelProjector
{
    /**
     * @param T $repository
     */
    public function __construct(
        ReadModelRepositoryInterface $repository = new InMemoryReadModelRepository(),
    ) {
        parent::__construct(repository: $repository);
    }
}
