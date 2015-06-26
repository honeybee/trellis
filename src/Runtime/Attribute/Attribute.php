<?php

namespace Trellis\Runtime\Attribute;

use Trellis\Common\Error\BadValueException;
use Trellis\Common\Error\InvalidTypeException;
use Trellis\Common\Error\InvalidConfigException;
use Trellis\Common\Object;
use Trellis\Runtime\EntityTypeInterface;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Validator;
use Trellis\Runtime\Validator\ValidatorInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\ValueHolder\ValueHolderInterface;

/**
 * Base class that all Trellis AttributeInterface implementations should extend.
 * Provides a pretty complete implementation for the interface, concrete
 * attributes should at least implement buildValidationRules in order to
 * provide validation rules that sanitize the given attribute value.
 *
 * basic options: 'validator', 'value', 'default_value', 'null_value', 'mandatory'
 * @todo extends Object; which introduces a breaking change to the create method.
 * TODO introduce 'mandatory' option
 */
abstract class Attribute implements AttributeInterface
{
    /**
     * Holds a reference to the attribute's type.
     *
     * @var EntityTypeInterface $type;
     */
    protected $type;

    /**
     * Holds a reference to the parent attribute, if there is one.
     *
     * @var AttributeInterface $parent;
     */
    protected $parent;

    /**
     * Holds the attribute's name.
     *
     * @var string $name
     */
    protected $name;

    /**
     * Holds the attribute's options.
     *
     * @var array $options
     */
    protected $options = [];

    /**
     * Holds the attribute's validator instance.
     *
     * @var ValidatorInterface $validator
     */
    protected $validator;

    /**
     * @var string fully qualified class name implementing ValueHolderInterface
     */
    protected $value_holder_implementor;

    /**
     * Constructs a new attribute instance.
     *
     * @param string $name
     * @param EntityTypeInterface $type
     * @param array $options
     * @param AttributeInterface $parent
     */
    public function __construct(
        $name,
        EntityTypeInterface $type,
        array $options = [],
        AttributeInterface $parent = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
        $this->parent = $parent;
    }

    /**
     * Returns the name of the attribute.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the attribute's entity type.
     *
     * @return EntityTypeInterface
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the attribute's parent, if it has one.
     *
     * @return AttributeInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Returns attribute path of this attribute. Depending on this attribute
     * being part of an embedded entity this may look like this format:
     * {attribute_name}.{type_prefix}.{attribute_name}
     *
     * @return string attribute path of this attribute
     */
    public function getPath()
    {
        return AttributePath::getAttributePath($this);
    }

    /**
     * Returns the attribute's top-most entity type. That is the entity type
     * of the root attribute of this attribute.
     *
     * @return EntityTypeInterface
     */
    public function getRootType()
    {
        return AttributePath::getRootEntityType($this);
    }

    /**
     * Returns the default value of the attribute.
     *
     * @return mixed value to be used/interpreted as the default value
     */
    public function getDefaultValue()
    {
        if ($this->hasOption(self::OPTION_DEFAULT_VALUE)) {
            return $this->getSanitizedValue(
                $this->getOption(self::OPTION_DEFAULT_VALUE, $this->getNullValue())
            );
        }

        return $this->getNullValue();
    }

    /**
     * Returns the attribute's null value.
     *
     * @return mixed value to be used/interpreted as null (not set)
     */
    public function getNullValue()
    {
        return null;
    }

    /**
     * Returns the ValidatorInterface implementation to use when validating values for this attribute.
     * Override this method if you want inject your own implementation.
     *
     * @return ValidatorInterface implementation
     */
    public function getValidator()
    {
        if (!$this->validator) {
            $default_validator_class = Validator::CLASS;
            $validator_implementor = $this->getOption(self::OPTION_VALIDATOR, $default_validator_class);

            if (!class_exists($validator_implementor, true)) {
                throw new InvalidConfigException(
                    sprintf(
                        "Unable to resolve validator implementor '%s' given for attribute '%s' on entity type '%s'.",
                        $validator_implementor,
                        $this->getName(),
                        $this->getType()->getName()
                    )
                );
            }

            $validator = new $validator_implementor($this->getName(), $this->buildValidationRules());
            if (!$validator instanceof ValidatorInterface) {
                throw new InvalidTypeException(
                    sprintf(
                        "Invalid validator implementor '%s' given for attribute '%s' on entity type '%s'. " .
                        "Make sure to implement '%s'.",
                        $validator_implementor,
                        $this->getName(),
                        $this->getType() ? $this->getType()->getName() : 'undefined',
                        ValidatorInterface::CLASS
                    )
                );
            }
            $this->validator = $validator;
        }

        return $this->validator;
    }

    /**
     * Creates a ValueHolderInterface, that is specific to the current attribute instance.
     *
     * @return ValueHolderInterface
     */
    public function createValueHolder($apply_default_values = false)
    {
        if (!$this->value_holder_implementor) {
            $implementor = $this->hasOption(self::OPTION_VALUE_HOLDER)
                ? $this->getOption(self::OPTION_VALUE_HOLDER)
                : $this->buildDefaultValueHolderClassName();

            if (!class_exists($implementor)) {
                throw new InvalidConfigException(
                    sprintf(
                        "Invalid valueholder implementor '%s' configured for attribute '%s' on entity '%s'.",
                        $implementor,
                        $this->getName(),
                        $this->getType() ? $this->getType()->getName() : 'undefined'
                    )
                );
            }

            $test_value_holder = new $implementor($this);

            if (!$test_value_holder instanceof ValueHolderInterface) {
                throw new InvalidTypeException(
                    sprintf(
                        "Invalid valueholder implementation '%s' given for attribute '%s' on entity type '%s'. " .
                        "Make sure to implement '%s'.",
                        $implementor,
                        $this->getName(),
                        $this->getType() ? $this->getType()->getName() : 'undefined',
                        ValueHolderInterface::CLASS
                    )
                );
            }

            $this->value_holder_implementor = $implementor;
        }

        $value_holder = new $this->value_holder_implementor($this);
        if ($apply_default_values === true) {
            $value_holder->setValue($this->getDefaultValue());
        } elseif ($apply_default_values === false) {
            $value_holder->setValue($this->getNullValue());
        } else {
            throw new InvalidTypeException(
                sprintf(
                    "Only boolean arguments are acceptable for attribute '%s' on entity type '%s'. ",
                    $this->getName(),
                    $this->getType() ? $this->getType()->getName() : 'undefined'
                )
            );
        }

        return $value_holder;
    }

    /**
     * Returns the attribute's options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns a attribute option by name if it exists.
     * Otherwise an optional default is returned.
     *
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        return $this->hasOption($name) ? $this->options[$name] : $default;
    }

    /**
     * Tells if a attribute currently owns a specific option.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Build a list of rules used by the attribute's validator to validate values for this attribute.
     *
     * @return RuleList
     */
    protected function buildValidationRules()
    {
        return new RuleList();
    }

    /**
     * @param mixed $value value to be assigned somewhere
     *
     * @return mixed sanitized version of the given value
     *
     * @throws BadValueException on invalid input value according to validation
     */
    protected function getSanitizedValue($value)
    {
        $validation_result = $this->getValidator()->validate($value);

        if ($validation_result->getSeverity() > IncidentInterface::NOTICE) {
            throw new BadValueException(
                sprintf(
                    "Given value for attribute '%s' on entity type '%s' is not valid.",
                    $this->getName(),
                    $this->getType() ? $this->getType()->getName() : 'undefined'
                )
            );
        }

        return $validation_result->getSanitizedValue();
    }

    /**
     * Returns the ValueHolderInterface implementation to use when aggregating (value)data for this attribute.
     * Override this method if you want inject your own implementation.
     *
     * @return string Fully qualified name of an ValueHolderInterface implementation.
     */
    protected function buildDefaultValueHolderClassName()
    {
        return preg_replace('#Attribute$#', 'ValueHolder', get_class($this));
    }
}
