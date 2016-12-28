<?php

namespace Trellis;

use Trellis\Entity\ValueObjectInterface;

interface EntityInterface
{
    /**
     * Returns the entity"s identifier.
     *
     * @return ValueObjectInterface
     */
    public function getIdentity(): ValueObjectInterface;

    /**
     * Tells whether this entity is considered equal to another given entity.
     * Entities are equal when they have the same type and values.
     *
     * @param EntityInterface $entity
     *
     * @return boolean true on both entities have the same type and values; false otherwise.
     */
    public function isSameAs(EntityInterface $entity): bool;
}
