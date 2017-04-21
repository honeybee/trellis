<?php

namespace Trellis\EntityType;

use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeMap;
use Trellis\Entity\TypedEntityInterface;

interface EntityTypeInterface
{
    /**
     * Returns the name of the type.
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the type"s prefix (technical identifier).
     * @return string
     */
    public function getPrefix(): string;

    /**
     * Returns the entity-types root-type.
     * @return EntityTypeInterface
     */
    public function getRoot(): EntityTypeInterface;

    /**
     * Returns the type"s parent-attribute, if it has one.
     * @return null|AttributeInterface
     */
    public function getParentAttribute(): ?AttributeInterface;

    /**
     * Returns the type"s parent-type, if it has one.
     * @return null|EntityTypeInterface
     */
    public function getParent(): ?EntityTypeInterface;

    /**
     * Tells if an entity-type has a parent-type, hence is a nested-type.
     * @return boolean
     */
    public function hasParent(): bool;

    /**
     * Tells if an entity-type is the root-type of an entity aggregate.
     * @return boolean
     */
    public function isRoot(): bool;

    /**
     * Tells if the type has an attribute with the given name.
     * @param string $typePath
     * @return boolean
     */
    public function hasAttribute(string $typePath): bool;

    /**
     * Returns a certain attribute by type-path.
     * @param string $typePath
     * @return AttributeInterface
     */
    public function getAttribute(string $typePath): AttributeInterface;

    /**
     * Returns the type"s attribute collection filter by a set of attribute types.
     * @param string[] $typePaths An optional list of type-paths to look for.
     * @return AttributeMap
     */
    public function getAttributes(array $typePaths = []): AttributeMap;

    /**
     * Creates a new EntityInterface instance.
     * @param mixed[] $entityState Optional state for initial hydration.
     * @param TypedEntityInterface $parent
     * @return TypedEntityInterface
     */
    public function makeEntity(array $entityState = [], TypedEntityInterface $parent = null): TypedEntityInterface;
}
