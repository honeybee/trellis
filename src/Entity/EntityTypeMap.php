<?php

namespace Trellis\Entity;

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
        return $this->hasKey($prefix) ? $this->getItem($prefix) : null;
    }

    /**
     * @param string $class
     *
     * @return EntityTypeInterface
     */
    public function byClassName($class)
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
    public function byName($name)
    {
        $found_types = $this->filter(function (EntityTypeInterface $entity_type) use ($name) {
            return $entity_type === $name;
        })->getValues();

        return count($found_types) == 1 ? $found_types[0] : null;
    }
}
