<?php

namespace Trellis;

use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeMap;

interface EntityTypeInterface
{
    /**
     * Returns the name of the type.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the type's prefix (technical identifier).
     *
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Returns the entity-types root-type.
     *
     * @return EntityTypeInterface
     */
    public function getRoot(): EntityTypeInterface;

    /**
     * Returns the type's parent-attribute, if it has one.
     *
     * @return null|AttributeInterface
     */
    public function getParentAttribute(): ?AttributeInterface;

    /**
     * Returns the type's parent-type, if it has one.
     *
     * @return null|EntityTypeInterface
     */
    public function getParent(): ?EntityTypeInterface;

    /**
     * Tells if an entity-type has a parent-type, hence is a nested-type.
     *
     * @return boolean
     */
    public function hasParent(): bool;

    /**
     * Tells if an entity-type is the root-type of an entity aggregate.
     *
     * @return boolean
     */
    public function isRoot(): bool;

    /**
     * Tells if the type has an attribute with the given name.
     *
     * @param string $type_path
     *
     * @return boolean
     */
    public function hasAttribute(string $type_path): bool;

    /**
     * Returns a certain attribute by type-path.
     *
     * @param string $type_path
     *
     * @return AttributeInterface
     */
    public function getAttribute(string $type_path): AttributeInterface;

    /**
     * Returns the type's attribute collection filter by a set of attribute types.
     *
     * @param string[] $type_paths An optional list of type-paths to look for.
     *
     * @return AttributeMap
     */
    public function getAttributes(array $type_paths = []): AttributeMap;

    /**
     * Creates a new EntityInterface instance.
     *
     * @param mixed[] $data Optional data for initial hydration.
     * @param EntityInterface $parent
     *
     * @return EntityInterface
     */
    public function makeEntity(array $data = [], EntityInterface $parent = null): EntityInterface;

    /**
     * @param $key
     *
     * @return bool
     */
    public function hasOption(string $key): bool;

    /**
     * @param string $key
     * @param mixed $default
     * @param boolean $fluent
     *
     * @return mixed
     */
    public function getOption(string $key, $default = null, bool $fluent = false);
}
