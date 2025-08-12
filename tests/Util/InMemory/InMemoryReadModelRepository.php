<?php

declare(strict_types=1);

namespace App\Tests\Util\InMemory;

use App\Domain\Shared\IdentifiableInterface;
use App\Domain\Shared\Persistence\ReadModelRepositoryInterface;
use App\Domain\Shared\ValueObject\ValueObjectInterface;

/**
 * @template T of IdentifiableInterface
 *
 * @template-implements ReadModelRepositoryInterface<T>
 */
class InMemoryReadModelRepository implements ReadModelRepositoryInterface
{
    /**
     * @param T[]  $readModels
     */
    public function __construct(
        private array $readModels = [],
    ) {
    }

    public function save(IdentifiableInterface $identifiableModel): void
    {
        $this->readModels[] = $identifiableModel;
    }

    public function update(IdentifiableInterface $identifiableModel): void
    {
        foreach ($this->readModels as $key => $readModel) {
            if ($readModel->getId() === $identifiableModel->getId()) {
                $this->readModels[$key] = $identifiableModel;
            }
        }
    }

    public function find(string $id): ?IdentifiableInterface
    {
        return array_find(
            $this->readModels,
            fn (IdentifiableInterface $readModel): bool => (string) $readModel->getId() === $id
        ) ?? null;
    }

    public function findBy(array $fields): array
    {
        return array_filter(
            array: $this->readModels,
            callback: function (IdentifiableInterface $readModel) use ($fields): bool {
                foreach ($fields as $field => $value) {
                    if (!property_exists($readModel, $field)) {
                        return false;
                    }

                    $propertyValue = $readModel->{$field};

                    if ($propertyValue instanceof ValueObjectInterface) {
                        return $propertyValue->getValue() === $value;
                    }

                    if (is_string($propertyValue)) {
                        return $propertyValue === $value;
                    }
                }

                return false;
            }
        );
    }

    public function findAll(): array
    {
        return $this->readModels;
    }

    public function remove(IdentifiableInterface $identifiableModel): void
    {
        foreach ($this->readModels as $key => $readModel) {
            if ($readModel->getId() === $identifiableModel->getId()) {
                unset($this->readModels[$key]);
            }
        }

        $this->readModels = array_values($this->readModels);
    }
}
