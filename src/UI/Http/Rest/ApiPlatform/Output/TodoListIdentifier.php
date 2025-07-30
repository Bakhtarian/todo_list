<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Output;

use ApiPlatform\Metadata\ApiProperty;
use App\Domain\Shared\ValueObject\AggregateRootId;

final readonly class TodoListIdentifier
{
    private function __construct(
        #[ApiProperty(
            description: 'The ID of the Todo list.',
            readable: true,
            writable: false,
            identifier: true,
            openapiContext: [
                'type' => 'string',
                'format' => 'uuid',
                'example' => 'e07b703b-0c83-4688-bf5f-aef5121727a6',
                'nullable' => false,
            ],
        )]
        public string $id,
    ) {
    }

    public static function create(AggregateRootId $aggregateRootId): self
    {
        return new self(id: $aggregateRootId->toString());
    }
}
