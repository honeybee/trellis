<?php

namespace Trellis\EntityType;

use Trellis\EntityInterface;
use Trellis\EntityTypeInterface;
use Trellis\Entity\ValueObjectInterface;

interface AttributeInterface
{
    /**
     * Returns the name of the attribute.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the attribute"s type.
     *
     * @return EntityTypeInterface
     */
    public function getEntityType(): EntityTypeInterface;

    /**
     * Returns the attribute"s parent, if it has one.
     *
     * @return null|AttributeInterface
     */
    public function getParent(): ?AttributeInterface;

    /**
     * Returns attribute path of this attribute. Depending on this attribute
     * being part of an embedded entity this may look like this format:
     * {attribute_name}.{type_prefix}.{attribute_name}
     *
     * @return string attribute path of this attribute
     */
    public function toPath(): string;

    /**
     * Create an attribute specific value instance for the given value.
     *
     * @param mixed $value
     * @param EntityInterface $parent The entity that the value is being created for.
     *
     * @return ValueObjectInterface
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface;

    /**
     * @param string $key
     * @param mixed $default
     * @param boolean $fluent
     *
     * @return mixed|Params
     */
    public function getParam(string $key, $default = null, bool $fluent = false);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasParam(string $key): bool;
}
