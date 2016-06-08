<?php

namespace Trellis\Runtime\Entity;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueKeyInterface;
use Trellis\Common\Collection\UniqueValueInterface;

class EntityMap extends TypedMap implements UniqueKeyInterface, UniqueValueInterface
{
    /**
     * Initializes the map with the provided entity identifiers as keys
     *
     * @param array $entities
     */
    public function __construct(array $entities = [])
    {
        foreach ($entities as $entity) {
            $this->ensureValidItemType($entity);
            $this->setItem($entity->getIdentifier(), $entity);
        }
    }

    /**
     * Returns the EntityInterface interface-name to the TypeMap parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return EntityInterface::CLASS;
    }
}
