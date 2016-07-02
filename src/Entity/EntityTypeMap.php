<?php

namespace Trellis\Entity;

use Trellis\Collection\TypedMap;

class EntityTypeMap extends TypedMap
{
    /**
     * @param EntityInterface[] $entities
     */
    public function __construct(array $entities)
    {
        parent::__construct(EntityTypeInterface::CLASS, $entities);
    }

    /**
     * @param string $prefix
     *
     * @return EntityTypeInterface
     */
    public function getEntityTypeByPrefix($prefix)
    {
        return $this->hasKey($prefix) ? $this->getItem($prefix) : null;
    }

    /**
     * @param string $class
     *
     * @return EntityTypeInterface
     */
    public function getEntityTypeByClass($class)
    {
        $found_types = $this->filter(function (EntityTypeInterface $entity_type) use ($class) {
            return get_class($entity_type) === $class;
        })->getValues();

        return count($found_types) == 1 ? $found_types[0] : null;
    }

    /**
     * @param string $name
     *
     * @return EntityTypeInterface
     */
    public function getEntityTypeByName($name)
    {
        $found_types = $this->filter(function (EntityTypeInterface $entity_type) use ($name) {
            return $entity_type === $name;
        })->getValues();

        return count($found_types) == 1 ? $found_types[0] : null;
    }
}
