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
     * @return EntityTypeInterface
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
     * Tells if the type has an attributw with the given name.
     *
     * @param string $type_path
     *
     * @return boolean
     */
    public function hasAttribute($type_path);

    /**
     * Returns a certain attribute by type-path.
     *
     * @param string $type_path
     *
     * @return AttributeInterface
     */
    public function getAttribute($type_path);

    /**
     * Returns the type's attribute collection filter by a set of attribute types.
     *
     * @param string[] $type_paths An optional list of type-paths to look for.
     *
     * @return AttributeMap
     */
    public function getAttributes(array $type_paths = []);

    /**
     * Returns an assoc array of attributes, the attribute names being the keys.
     *
     * @return AttributeInterface[]
     */
    public function getDefaultAttributes();

    /**
     * Returns an array containing the names of the type's default attributes.
     *
     * @return string[]
     */
    public function getDefaultAttributeNames();

    /**
     * Creates a new EntityInterface instance.
     *
     * @param mixed[] $data Optional data for initial hydration.
     * @param EntityInterface $parent
     *
     * @return EntityInterface
     */
    public function createEntity(array $data = [], EntityInterface $parent = null);

    /**
     * @return string Fully qualified class name of the entity type that is utilized by the current type.
     */
    public function getEntityImplementor();
}
