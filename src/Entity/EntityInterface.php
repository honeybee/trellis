<?php

namespace Trellis\Entity;

interface EntityInterface
{
    const ENTITY_TYPE = '@type';

    /**
     * Returns the entity's identifier. This might be a composite
     * of multiple attribute values or a UUID or similar.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the entity's type.
     *
     * @return EntityTypeInterface
     */
    public function type();

    /**
     * Returns the entity's parent, if it has one.
     *
     * @return EntityInterface
     */
    public function parent();

    /**
     * Returns the entity's root, if it has one.
     *
     * @return EntityInterface
     */
    public function root();

    /**
     * Returns the value for a specific attribute.
     *
     * @param string|string[]|null $value_path
     *
     * @return mixed|mixed[]|ValueMap
     */
    public function get($value_path = null);

    /**
     * Tells if the entity has a value set for a given attribute.
     *
     * @param string $attribute_name
     *
     * @return boolean
     */
    public function has($attribute_name);

    /**
     * @param EntityInterface $other
     * @param bool $as_array
     *
     * @return ValueMap|mixed[]
     */
    public function diff(EntityInterface $other, $as_array = false);

    /**
     * @param string $attribute_name
     * @param mixed $value
     *
     * @return EntityInterface
     */
    public function with($attribute_name, $value);

    /**
     * @param mixed[] $values
     *
     * @return EntityInterface
     */
    public function withValues(array $values);

    /**
     * Tells whether this entity is considered equal to another given entity.
     * Entities are equal when they have the same type and values.
     *
     * @param EntityInterface $entity
     *
     * @return boolean true on both entities have the same type and values; false otherwise.
     */
    public function isEqualTo(EntityInterface $entity);

    /**
     * Returns an array representation of a entity's current value state.
     *
     * @return mixed[]
     */
    public function toArray();

    /**
     * Returns a path-spec, that describes an entities current location within an aggregate.
     *
     * @return string
     */
    public function toValuePath();
}
