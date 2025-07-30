<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Input;

use ApiPlatform\Metadata\ApiProperty;

final readonly class SetDescriptionForListInput
{
    public function __construct(
        #[ApiProperty(
            description: 'The description to set for the Todo List.',
            openapiContext: [
                'type' => 'string',
                'example' => 'What a day to be alive and add a description to my list.',
                'nullable' => true,
            ],
        )]
        public ?string $description,
    ) {
    }
}
