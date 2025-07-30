<?php

namespace App\Domain\Shared\Event;

use App\Domain\Shared\SerializableInterface;

/**
 * @template-extends SerializableInterface<EventInterface>
 */
interface EventInterface extends SerializableInterface
{
    public function getAggregateId(): \Stringable;
}
