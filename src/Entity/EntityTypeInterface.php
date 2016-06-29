<?php

namespace Trellis\Entity;

interface EntityTypeInterface
{
    /**
     * Returns the name of the type.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the type's prefix (technical identifier).
     *
     * @return string
     */
    public function getPrefix();

    /**
     * Returns the entity-types root-type.
     *
     * @return EntityInterface
     */
    public function getRootType();

    /**
     * Returns the type's parent-attribute, if it has one.
     *
     * @return AttributeInterface
     */
    public function getParentAttribute();

    /**
     * Returns the type's parent-type, if it has one.
     *
     * @return EntityTypeInterface
     */
    public function getParent();

    /**
     * Tells if an entity-type has a parent-type, hence is a nested-type.
     *
     * @return boolean
     */
    public function hasParent();

    /**
     * Tells if an entity-type is the root-type of an entity aggregate.
     *
     * @return boolean
     */
    public function isRoot();

    /**
     * Returns the type's attribute collection filter by a set of attribute types.
     *
     * @param array $attribute_types A list of attribute-classes to filter for.
     *
     * @return AttributeMap
     */
    public function getAttributes();

    /**
     * Returns the type's attribute collection filtered by a set of attribute names.
     *
     * @param array $attribute_names A list of attribute-names to filter for.
     *
     * @return AttributeMap
     */
    public function getAttributesByName(array $attribute_names = []);

    /**
     * Returns the type's attribute collection filter by a set of attribute types.
     *
     * @param array $attribute_types A list of attribute-classes to filter for.
     *
     * @return AttributeMap
     */
    public function getAttributesByType(array $attribute_types = []);

    /**
     * Tells if the type has an attributw with the given name.
     *
     * @return bool
     */
    public function hasAttribute($attribute_name);

    /**
     * Returns a certain type attribute by name.
     *
     * @param string $name
     *
     * @return AttributeInterface
     */
    public function getAttribute($name);

    /**
     * Returns a map of path indexed attributes satisifed by the given filter/callback predicate.
     *
     * @param Closure $filter Returns a boolean for each element, that shall be contained within the resulting map.
     * @param boolean $recursive
     *
     * @return AttributeMap wth attribute_path => $attribute
     */
    public function collateAttributes(Closure $filter, $recursive = true);

    /**
     * Creates a new EntityInterface instance.
     *
     * @param array $data Optional data for initial hydration.
     * @param EntityInterface $parent
     *
     * @return EntityInterface
     */
    public function createEntity(array $data = [], EntityInterface $parent = null);

    /**
     * @return string Fully qualified class name of the entity type that is utilized by the current type.
     */
    public function getEntityImplementor();

    /**
     * Returns an array containing the names of the type's default attributes.
     *
     * @return string[]
     */
    public function getDefaultAttributeNames();

    /**
     * Returns an assoc array of attributes, the attribute names being the keys.
     *
     * @return AttributeInterface[]
     */
    public function getDefaultAttributes();

    /**
     * Returns the attribute that is pointed at by the given attribute path.
     *
     * @return AttributeInterface
     */
    public function getAttributeByPath($attribute_path);
}
