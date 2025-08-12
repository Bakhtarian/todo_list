<?php

declare(strict_types=1);

namespace App\Tests\Todo\Util;

use App\Infrastructure\TodoList\Persistence\Read\Overview\Overview;
use App\Tests\Util\InMemory\InMemoryReadModelRepository;

/**
 * @template-extends InMemoryReadModelRepository<Overview>
 */
final class InMemoryOverviewRepository extends InMemoryReadModelRepository
{
}
