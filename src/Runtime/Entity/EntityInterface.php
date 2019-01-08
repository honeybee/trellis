<?php

namespace Trellis\Runtime\Entity;

use Trellis\Runtime\EntityTypeInterface;
use Trellis\Runtime\Validator\Result\ResultMap;
use Trellis\Runtime\ValueHolder\ValueChangedEvent;
use Trellis\Runtime\ValueHolder\ValueChangedEventList;

/**
 * An EntityInterface is a generic container for structured data.
 * It provides access to values on a per attribute base.
 */
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
     * @return EntityInterface|null
     */
    public function getParent();

    /**
     * Returns the entity's root, if it has one.
     *
     * @return EntityInterface|null
     */
    public function getRoot();

    /**
     * Sets a specific value by attribute_name.
     *
     * @param string $attribute_name
     * @param mixed $value
     */
    public function setValue($attribute_name, $value);

    /**
     * Batch set a given list of attribute values.
     *
     * @param array $values
     */
    public function setValues(array $values);

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
     * Returns a (de)serializable representation of the attribute values. The
     * returned values MUST be acceptable as values on the attributes (that is,
     * their respective valueholders) to reconstitute them.
     *
     * Instead of implementing an explicit fromNative method use setValues to
     * recreate an entity from the given native representation.
     *
     * @return array of attribute values that can be used for serializing/deserializing
     */
    public function toNative();

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
     * Returns a path-spec, that describes an entities current (embed) location within an entity aggregate.
     *
     * @return string
     */
    public function asEmbedPath();

    /**
     * Returns the validation results of a prior call to setValue(s).
     * There will be a result for each affected attribute.
     *
     * @return ResultMap
     */
    public function getValidationResults();

    /**
     * Tells if a entity is considered being in a valid/safe state.
     * A entity is considered valid if no errors have occured while consuming data.
     *
     * @return boolean
     */
    public function isValid();

    /**
     * Returns a list of all events that have occured since the entity was instanciated
     * or the 'markClean' method was called.
     *
     * @return ValueChangedEventList
     */
    public function getChanges();

    /**
     * Tells if the current entity instance is clean,
     * hence if it has any unhandled changes.
     *
     * @return boolean
     */
    public function isClean();

    /**
     * Marks the current entity instance as clean, hence resets the all tracked changed.
     */
    public function markClean();

    /**
     * Returns the entity's type.
     *
     * @return EntityTypeInterface
     */
    public function getType();

    /**
     * Attaches the given entity-changed listener.
     *
     * @param EntityChangedListenerInterface $listener
     */
    public function addEntityChangedListener(EntityChangedListenerInterface $listener);

    /**
     * Removes the given entity-changed listener.
     *
     * @param EntityChangedListenerInterface $listener
     */
    public function removeEntityChangedListener(EntityChangedListenerInterface $listener);
}
