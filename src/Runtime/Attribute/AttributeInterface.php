<?php

namespace Trellis\Runtime\Attribute;

use Trellis\Runtime\Attribute\Value\ValueHolderInterface;
use Trellis\Runtime\EntityTypeInterface;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Validator\ValidatorInterface;

/**
 * AttributeInterfaces hold meta data that is used to model entity properties,
 * hence your data's behaviour concerning consistent containment.
 */
interface AttributeInterface
{
    const OPTION_DEFAULT_VALUE = 'default_value';
    const OPTION_NULL_VALUE = 'null_value';
    const OPTION_VALUE_HOLDER = 'value_holder';
    const OPTION_VALIDATOR = 'validator';

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
    public function getType();

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
    public function getPath();

    /**
     * Returns the attribute's top-most entity type. That is the entity type
     * of the root attribute of this attribute.
     *
     * @return EntityTypeInterface
     */
    public function getRootType();

    /**
     * Returns the default value of the attribute.
     *
     * @return mixed value to be used/interpreted as the default value
     */
    public function getDefaultValue();

    /**
     * Returns a attribute's null value.
     *
     * @return mixed value to be used/interpreted as null (not set)
     */
    public function getNullValue();

    /**
     * Returns a attribute option by name if it exists.
     * Otherwise an optional default is returned.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null);

    /**
     * Tells if a attribute currently owns a specific option.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasOption($name);

    /**
     * @return ValidatorInterface
     */
    public function getValidator();

    /**
     * Creates a ValueHolderInterface instance dedicated to the current attribute instance.
     *
     * @return ValueHolderInterface
     */
    public function createValueHolder();
}
