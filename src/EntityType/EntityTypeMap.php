<?php

namespace Trellis\EntityType;

use Trellis\Collection\TypedMap;

class EntityTypeMap extends TypedMap
{
    /**
     * @param EntityTypeInterface[] $entity_types
     */
    public function __construct(array $entity_types)
    {
        parent::__construct(EntityTypeInterface::CLASS, $entity_types);
    }

    /**
     * @param string $prefix
     *
     * @return EntityTypeInterface
     */
    public function byPrefix($prefix)
    {
        $found_types = array_values($this->filter(function (EntityTypeInterface $entity_type) use ($prefix) {
            return $entity_type->getPrefix() === $prefix;
        })->getItems());

        return !empty($found_types) ? $found_types[0] : null;
    }

    /**
     * @param string $class
     *
     * @return EntityTypeInterface
     */
    public function byClassName($class)
    {
        $found_types = array_values($this->filter(function (EntityTypeInterface $entity_type) use ($class) {
            return get_class($entity_type) === $class;
        })->getItems());

        return !empty($found_types) ? $found_types[0] : null;
    }

    /**
     * @param string $name
     *
     * @return EntityTypeInterface
     */
    public function byName($name)
    {
        $found_types = array_values($this->filter(function (EntityTypeInterface $entity_type) use ($name) {
            return $entity_type->getName() === $name;
        })->getItems());

        return !empty($found_types) ? $found_types[0] : null;
    }
}
