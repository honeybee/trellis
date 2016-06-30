<?php

namespace Trellis\Entity;

use Trellis\Collection\TypedMap;

class EntityMap extends TypedMap
{
    /**
     * @param EntityInterface[] $entities
     */
    public function __construct(array $entities)
    {
        parent::__construct(EntityInterface::CLASS, $entities);
    }
}
