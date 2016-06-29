<?php

namespace Trellis\Collection;

use Closure;

interface CollectionInterface extends \Iterator, \Countable, \ArrayAccess
{
    /**
     * Return a specific value from the collection for the given key.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function getValue($key);

    /**
     * Return a list of specific values from the collection for the given keys.
     *
     * @param array $keys
     *
     * @return mixed
     */
    public function getValues(array $keys = []);

    /**
     * Add the given key-value pair to the collection.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return CollectionInterface
     */
    public function withValue($key, $value);

    /**
     * Adds the given key-values pairs to the collection.
     *
     * @param mixed[] $values
     *
     * @return CollectionInterface
     */
    public function withValues($values);

    /**
     * Remove the given value from the collection.
     *
     * @param mixed $value
     *
     * @return CollectionInterface
     */
    public function withoutValue($value);

    /**
     * Remove the given values from the collection.
     *
     * @param array $values
     *
     * @return CollectionInterface
     */
    public function withoutValues(array $values = []);

    /**
     * Tells if the collection has an value set for the given key.
     *
     * @param mixed $key
     *
     * @return boolean
     */
    public function hasKey($key);

    /**
     * Return the key for the given value.
     *
     * @param mixed $value
     *
     * @return mixed Returns false, if the value is not contained.
     */
    public function getKey($value);

    /**
     * Tells if the collection contains the given value.
     *
     * @param mixed $value
     *
     * @return boolean
     */
    public function hasValue($value);

    /**
     * Returns the size of the collection.
     *
     * @return int
     */
    public function getSize();

    /**
     * Tells if the collection is empty.
     *
     * @return boolean
     */
    public function isEmpty();

    /**
     * Filters the collection by invoking the given callback on each item, expecting a boolean return.
     *
     * @param Closure $callback
     *
     * @return CollectionInterface
     */
    public function filter(Closure $callback);

    /**
     * Returns an array representation of the collection's contained values.
     *
     * @return array
     */
    public function toArray();
}
