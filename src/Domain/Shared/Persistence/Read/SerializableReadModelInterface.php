<?php

namespace App\Domain\Shared\Persistence\Read;

use App\Domain\Shared\IdentifiableInterface;
use App\Domain\Shared\SerializableInterface;

/**
 * @template T
 *
 * @template-extends SerializableInterface<T>
 */
interface SerializableReadModelInterface extends SerializableInterface, IdentifiableInterface
{
}
