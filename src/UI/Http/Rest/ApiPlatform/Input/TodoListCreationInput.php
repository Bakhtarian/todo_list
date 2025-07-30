<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Input;

use ApiPlatform\Metadata\ApiProperty;

final readonly class TodoListCreationInput
{
    /**
     * @param non-falsy-string $title
     */
    public function __construct(
        #[ApiProperty(
            description: 'The title of the Todo List.',
            openapiContext: [
                'type' => 'string',
                'example' => 'My Productive Day: Tasks to Accomplish',
                'nullable' => false,
            ]
        )]
        public string $title,
    ) {
    }
}
