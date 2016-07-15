<?php

namespace Trellis\EntityType\Attribute;

use Trellis\Entity\EntityInterface;

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
     * @return \Trellis\EntityType\EntityTypeInterface
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
     * Create an attribute specific value instance for the given value.
     *
     * @param EntityInterface $parent The entity that the value is being created for.
     * @param mixed $value
     *
     * @return \Trellis\Entity\Value\ValueInterface
     */
    public function createValue(EntityInterface $parent, $value = null);
}
