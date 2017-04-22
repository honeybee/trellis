<?php

namespace Trellis\EntityType;

use Honeybee\Frames\TypedMapTrait;

final class EntityTypeMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    /**
     * @param EntityTypeInterface[] $entityTypes
     */
    public function __construct(array $entityTypes = [])
    {
        $this->init(array_reduce($entityTypes, function (array $carry, EntityTypeInterface $entityType) {
            $carry[$entityType->getPrefix()] = $entityType; // enforce consistent attribute keys
            return $carry;
        }, []), EntityTypeInterface::class);
    }

    /**
     * @param string $name
     * @return null|EntityTypeInterface
     */
    public function byName(string $name): ?EntityTypeInterface
    {
        foreach ($this as $type) {
            if ($type->getName() === $name) {
                return $type;
            }
        }
        return null;
    }

    /**
     * @param string $className
     * @return null|EntityTypeInterface
     */
    public function byClassName(string $className): ?EntityTypeInterface
    {
        foreach ($this as $type) {
            if (get_class($type) === $className) {
                return $type;
            }
        }
        return null;
    }
}
