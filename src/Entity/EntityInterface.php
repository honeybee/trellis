<?php

namespace Trellis\Entity;

interface EntityInterface
{
    /**
     * Returns the entity's identifier. This might be a composite
     * of multiple attribute values or a UUID or similar.
     *
     * @return string
     */
    public function getIdentifier();

    /**
     * Returns the entity's parent, if it has one.
     *
     * @return EntityInterface
     */
    public function getParent();

    /**
     * Returns the entity's root, if it has one.
     *
     * @return EntityInterface
     */
    public function getRoot();

    /**
     * Returns the value for a specific attribute.
     *
     * @param string $attribute_name
     *
     * @return mixed
     */
    public function getValue($attribute_name);

    /**
     * Tells if the entity has a value set for a given attribute.
     *
     * @param string $attribute_name
     *
     * @return boolean
     */
    public function hasValue($attribute_name);

    /**
     * Returns the values of all our attributes or a just specific attribute subset,
     * that can be defined by the optional '$attribute_names' parameter.
     *
     * @param array $attribute_names
     *
     * @return array
     */
    public function getValues(array $attribute_names = []);

    /**
     * Returns an array representation of a entity's current value state.
     *
     * @return array
     */
    public function toArray();

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
     * Returns a path-spec, that describes an entities current location within an aggregate.
     *
     * @return string
     */
    public function asTrellisPath();

    /**
     * Returns the entity's type.
     *
     * @return EntityTypeInterface
     */
    public function getType();
}
