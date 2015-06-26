<?php

namespace Trellis\Runtime\ValueHolder;

interface ComplexValueInterface
{
    /**
     * @return array names of properties that are mandatory for this value object
     */
    public function getMandatoryPropertyNames();

    /**
     * @return array names of properties this value object has
     */
    public function getPropertyNames();

    /**
     * Returns a (de)serializable representation of the internal value.
     *
     * @return array value that can be used for serializing/deserializing
     */
    public function toNative();

    /**
     * @return boolean true if array keys/values are considered the same as this value object
     */
    public function similarToArray(array $other);

    /**
     * @param ComplexValueInterface $other
     *
     * @return boolean true if the other value object is of the same type and has the same values
     */
    public function similarTo(ComplexValueInterface $other);

    /**
     * Creates a new instance of the value object with some different data. The
     * initial data is the current data and merged with the given new data.
     *
     * @param array $data key value pairs to merge into the new value object
     *
     * @return ComplexValueInterface new instance created from current and given data
     */
    public function createWith(array $data);

    /**
     * Creates a new instance of the value object.
     *
     * @param array $data key value pairs to create a value object from
     *
     * @return ComplexValueInterface instance created from the given data
     */
    public static function createFromArray(array $data);
}
