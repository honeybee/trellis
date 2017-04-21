<?php

namespace Trellis\Entity;

use Trellis\ValueObject\ValueObjectInterface;

interface EntityInterface
{
    /**
     * @param array  $entityAsArray
     * @return EntityInterface
     */
    public static function fromArray(array $entityAsArray): EntityInterface;

    /**
     * Returns the entity"s identifier.
     * @return ValueObjectInterface
     */
    public function getIdentity(): ValueObjectInterface;

    /**
     * Tells whether this entity is considered equal to another given entity.
     * Entities are equal when they have the same type and values.
     * @param EntityInterface $entity
     * @return boolean true on both entities have the same type and values; false otherwise.
     */
    public function isSameAs(EntityInterface $entity): bool;

    /**
     * @param string $attributeName
     * @param mixed $value
     * @return EntityInterface
     */
    public function withValue(string $attributeName, $value): EntityInterface;

    /**
     * @param mixed[] $values
     * @return EntityInterface
     */
    public function withValues(array $values): EntityInterface;

    /**
     * Returns the value for a specific attribute.
     * @param string $valuePath
     * @return ValueObjectInterface
     */
    public function get(string $valuePath): ValueObjectInterface;

    /**
     * Tells if the entity has a value set for a given attribute.
     * @param string $attributeName
     * @return boolean
     */
    public function has(string $attributeName): bool;

    /**
     * @return mixed[]
     */
    public function toArray(): array;
}
