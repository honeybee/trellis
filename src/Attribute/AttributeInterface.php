<?php

namespace Trellis\Attribute;

interface AttributeInterface
{
    /**
     * Returns the name of the attribute.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the attribute's type.
     *
     * @return EntityTypeInterface
     */
    public function getEntityType();

    /**
     * Returns the attribute's parent, if it has one.
     *
     * @return AttributeInterface
     */
    public function getParent();

    /**
     * Returns attribute path of this attribute. Depending on this attribute
     * being part of an embedded entity this may look like this format:
     * {attribute_name}.{type_prefix}.{attribute_name}
     *
     * @return string attribute path of this attribute
     */
    public function toTypePath();

    /**
     * Returns the attribute's top-most entity type. That is the entity type
     * of the root attribute of this attribute.
     *
     * @return EntityTypeInterface
     */
    public function getRootEntityType();

    /**
     * Create an attribute specific value instance for the given value.
     *
     * @param mixed $value
     *
     * @return ValueInterface
     */
    public function createValue($value = null);
}
