<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\ApiPlatform\Output;

use ApiPlatform\Metadata\ApiProperty;
use App\Infrastructure\TodoList\Persistence\Read\DetailView\DetailView;

final readonly class TodoList
{
    public function __construct(
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
        #[ApiProperty(
            description: 'The title of the Todo list.',
            openapiContext: [
                'type' => 'string',
                'example' => 'My Productive Day: Tasks to Accomplish',
                'nullable' => false,
            ],
        )]
        public string $title,
        #[ApiProperty(
            description: 'When the Todo list has been created.',
            openapiContext: [
                'type' => 'string',
                'format' => 'date-time',
                'example' => '2025-01-01T00:00:00+00:00',
                'nullable' => false,
            ],
        )]
        public string $createdAt,
        #[ApiProperty(
            description: 'Small piece of text describing the list.',
            openapiContext: [
                'type' => 'string',
                'example' => 'This list will help me out with my productive day.',
                'nullable' => true,
            ],
        )]
        public ?string $description = null,
        #[ApiProperty(
            description: 'When the Todo List should be finished.',
            openapiContext: [
                'type' => 'string',
                'format' => 'date-time',
                'example' => '2026-01-01T00:00:00+00:00',
                'nullable' => true,
            ],
        )]
        public ?string $deadline = null,
        #[ApiProperty(
            description: 'Whether or not all the items have been finished.',
            openapiContext: [
                'type' => 'bool',
                'example' => false,
                'nullable' => false,
            ],
        )]
        public bool $isFinished = false,
    ) {
    }

    public static function create(DetailView $detailView): self
    {
        return new self(
            id: $detailView->id,
            title: $detailView->title->toString(),
            createdAt: $detailView->createdAt->toString(),
            description: $detailView->description?->toString(),
            deadline: $detailView->deadline?->toString(),
            isFinished: $detailView->finished,
        );
    }
}
