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
}
