<?php

namespace Trellis\Collection;

use Assert\Assertion;
use Closure;
use Trellis\Exception;

/**
 * Generic base implementation of the CollectionInterface interface.
 */
abstract class Collection implements CollectionInterface
{
    /**
     * Holds the collection's current values.
     *
     * @var array
     */
    protected $values = [];

    public function __construct(array $values = [])
    {
        $this->guardConstraints($values);

        $this->values = $values;
    }

    // Php Interface - Countable

    /**
     * Implementation of php's 'countable' interface's 'count' method.
     *
     * @return int
     *
     * @see http://php.net/manual/en/class.countable.php
     */
    public function count()
    {
        return count($this->values);
    }

    // Php Interface - ArrayAccess

    /**
     * Tells whether or not an offset exists.
     *
     * @param mixed $offset
     *
     * @return boolean
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetExists($offset)
    {
        if (!is_int($offset) && !is_string($offset)) {
            throw new Exception("Invalid offset type:" . gettype($offset));
        }

        return array_key_exists($offset, $this->values);
    }

    /**
     * Returns the value at specified offset.
     *
     * @param mixed $offset
     *
     * @return mixed
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->values[$offset];
        }

        return null;
    }

    /**
     * Assigns a value to the specified offset.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception("Collections are immutable and may not be directly modified.");
    }

    /**
     * Unsets the value at the given offset.
     *
     * @param mixed $offset
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetUnset($offset)
    {
        throw new Exception("Collections are immutable and may not be directly modified.");
    }

    // Php Interface - Iterator

    /**
     * Returns the pointer's current position/key.
     *
     * @return mixed
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function key()
    {
        return key($this->values);
    }

    /**
     * Returns the value for our current list-pointer position.
     *
     * @return mixed
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function current()
    {
        return current($this->values);
    }

    /**
     * Advance the internal list pointer to the next value.
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function next()
    {
        next($this->values);
    }

    /**
     * Reset the internal list pointer to position 0.
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function rewind()
    {
        reset($this->values);
    }

    /**
     * Tells if the list pointer is within the collection's boundry.
     *
     * @return boolean
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function valid()
    {
        $key = $this->key();
        return isset($this->values[$key]);
    }

    // Interface - CollectionInterface

    /**
     * {@inheritdoc}
     */
    public function getValue($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(array $keys = [])
    {
        if (empty($keys)) {
            return $this->values;
        }

        return array_filter(
            $this->values,
            function($key) use ($keys) {
                return in_array($key, $keys);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * {@inheritdoc}
     */
    public function withValue($key, $value)
    {
        if ($this->getValue($key) === $value) {
            return $this;
        }
        $this->guardConstraints([ $key => $value ]);

        $copy = clone $this;
        $copy->values[$key] = $value;

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function withValues($values)
    {
        $this->guardConstraints($values);
        $copied_values = array_merge($this->values, $values);

        $copy = clone $this;
        $copy->values = $values;

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutValue($value)
    {
        if ($key = $this->getKey($value)) {
            $copy = clone $this;
            unset($copy->values[$key]);
            return $copy;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutValues(array $values = [])
    {
        $copied_values = array_diff($this->values, $values);
        if (count($copied_values) === $this->getSize()) {
            return $this;
        }
        $copy = clone $this;
        $copy->values = $copied_values;

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function hasKey($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getKey($value)
    {
        $keys = array_keys($this->values, $value, true);

        return count($keys) > 0 ? $keys[0] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue($value)
    {
        return $this->getKey($value) !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->count();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->getSize() === 0;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(Closure $callback)
    {
        $filtered_values = array_filter($this->values, $callback);
        if (count($filtered_values) === $this->getSize()) {
            return $this;
        }
        $copy = clone $this;
        $copy->values = $filtered_values;

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_map(
            static function ($value) {
                if (is_callable([ $value, 'toArray' ])) {
                    $value = $value->toArray();
                }
                return $value;
            },
            $this->values
        );
    }

    /**
     * Make sure that the given values ahere to the constraints that apply to a specific collection.
     *
     * @param mixed[] $values
     */
    protected function guardConstraints(array $values)
    {
        $valueCountTable = [];
        foreach ($values as $key => $value) {
            if (!isset($valueCountTable[$value])) {
                $valueCountTable[$value] = 0;
            }
            $valueCountTable[$value]++;
            if ($this instanceof UniqueValueInterface) {
                Assertion::max($valueCountTable[$value], 1, 'Value within this collection must be unique.');
            }
        }
    }
}
