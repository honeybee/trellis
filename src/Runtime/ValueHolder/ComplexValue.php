<?php

namespace Trellis\Runtime\ValueHolder;

use Assert;
use Trellis\Common\Error\BadValueException;
use Trellis\Common\Error\RuntimeException;
use Trellis\Common\Object;

abstract class ComplexValue extends Object implements ComplexValueInterface
{
    const VALUE_TYPE_BOOLEAN = 'boolean';
    const VALUE_TYPE_INTEGER = 'integer';
    const VALUE_TYPE_FLOAT   = 'float';
    const VALUE_TYPE_ARRAY   = 'array';
    const VALUE_TYPE_TEXT    = 'text';
    const VALUE_TYPE_URL     = 'url';

    /**
     * @var array $values properties of this value object with their (default) values
     */
    protected $values = [];

    /**
     * @return array names of properties that are mandatory for this value object
     */
    public static function getMandatoryPropertyNames()
    {
        return [];
    }

    /**
     * @return array names of properties this value object has
     */
    public static function getPropertyMap()
    {
        return [];
    }

    /**
     * Creates a new instance.
     *
     * @param array $data key value pairs to create the value object from
     */
    public function __construct(array $data)
    {
        // check for mandatory property values
        foreach ($this->getMandatoryPropertyNames() as $name) {
            if (array_key_exists($name, $data)) {
                Assert\that($data[$name])->notEmpty();
            } else {
                throw new BadValueException('No "' . $name . '" property given.');
            }
        }

        // set only the known properties from the given data
        foreach ($this->values as $name => $default_value) {
            if (array_key_exists($name, $data)) {
                $this->values[$name] = $data[$name];
            }
        }
    }

    /**
     * Returns a (de)serializable representation of the internal value.
     *
     * @return array value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        return $this->values;
    }

    public function toArray()
    {
        return $this->values;
    }

    /**
     * Creates a new instance of the value object.
     *
     * @param array $data key value pairs to create a value object from
     *
     * @return ComplexValueInterface instance created from the given data
     */
    public static function createFromArray(array $data)
    {
        return new static($data);
    }

    /**
     * Creates a new instance of the value object with some different data. The
     * initial data is the current data and merged with the given new data.
     *
     * @param array $data key value pairs to merge into the new value object
     *
     * @return ComplexValueInterface new instance created from current and given data
     */
    public function createWith(array $data)
    {
        return new static(array_merge($this->toNative(), $data));
    }

    /**
     * @return boolean true if array keys/values are considered the same as this value object
     */
    public function similarToArray(array $other)
    {
        return $this->similarArrays($this->values, $other);
    }

    /**
     * @param ComplexValueInterface $other
     *
     * @return boolean true if the other value object is of the same type and has the same values
     */
    public function similarTo(ComplexValueInterface $other)
    {
        if (get_class($this) !== get_class($other)) {
            return false;
        }

        return $this->similarToArray($other->toNative());
    }

    /**
     * Lazy fallback to support get* methods on the value object when
     * the actual classes do not provide getters themselves. To prevent
     * this behaviour override this method.
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        if (mb_substr($name, 0, 3) === 'get') {
            $property_name = mb_substr($name, 3);
            if (!ctype_lower($property_name)) {
                $property_name = mb_strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $property_name));
            }
            if (array_key_exists($property_name, $this->values)) {
                return $this->values[$property_name];
            }
        }

        throw new RuntimeException('Method "' . $name . '" not supported on instance of class: ' . static::CLASS);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->values[0];
    }

    protected function similarArrays(array $data, array $other_data)
    {
        $keys = array_keys($data);
        $other_keys = array_keys($other_data);

        if (count($keys) !== count($other_keys)) {
            return false; // different number of keys is not considered equal
        }

        // each value of the first array should be the same in the second array
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $other_data)) {
                return false;
            }

            // TODO do we support nested arrays in a valueobject? if yes this and the toArray/toNative must change
            $has_equal_value = ($other_data[$key] === $value);
            if (!$has_equal_value) {
                return false;
            }
        }

        return true;
    }
}
