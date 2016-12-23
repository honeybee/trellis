<?php

namespace Trellis;

use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObjectMap;

interface DomainEntityInterface extends EntityInterface
{
    const ENTITY_TYPE = '@type';

    /**
     * @param string $attribute_name
     * @param mixed $value
     *
     * @return DomainEntityInterface
     */
    public function withValue(string $attribute_name, $value): DomainEntityInterface;

    /**
     * @param mixed[] $values
     *
     * @return DomainEntityInterface
     */
    public function withValues(array $values): DomainEntityInterface;

    /**
     * @return ValueObjectMap
     */
    public function getValueObjectMap(): ValueObjectMap;

    /**
     * Returns the value for a specific attribute.
     *
     * @param string $value_path
     *
     * @return ValueObjectInterface
     */
    public function get(string $value_path): ValueObjectInterface;

    /**
     * Tells if the entity has a value set for a given attribute.
     *
     * @param string $attribute_name
     *
     * @return boolean
     */
    public function has(string $attribute_name): bool;

    /**
     * Returns the entity's root, if it has one.
     *
     * @return DomainEntityInterface
     */
    public function getEntityRoot(): DomainEntityInterface;

    /**
     * Returns the entity's parent, if it has one.
     *
     * @return DomainEntityInterface|null
     */
    public function getEntityParent(): ?DomainEntityInterface;

    /**
     * Returns the entity's type.
     *
     * @return EntityTypeInterface
     */
    public function getEntityType(): EntityTypeInterface;

    /**
     * Returns an array representation of a entity's current value state.
     *
     * @return mixed[]
     */
    public function toNative(): array;

    /**
     * Returns a path-spec, that describes an entities current location within a graph.
     *
     * @return string
     */
    public function toPath(): string;
}
