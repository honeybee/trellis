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
     * Holds the collection's current items.
     *
     * @var mixed[]
     */
    protected $items = [];

    public function __construct(array $items = [])
    {
        $this->guardConstraints($items);

        $this->items = $items;
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
        return count($this->items);
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

        return array_key_exists($offset, $this->items);
    }

    /**
     * Returns the item at specified offset.
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
            return $this->items[$offset];
        }

        return null;
    }

    /**
     * Assigns a item to the specified offset.
     *
     * @param mixed $offset
     * @param mixed $item
     *
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetSet($offset, $item)
    {
        throw new Exception("Collections are immutable and may not be directly modified.");
    }

    /**
     * Unsets the item at the given offset.
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
        return key($this->items);
    }

    /**
     * Returns the item for our current list-pointer position.
     *
     * @return mixed
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function current()
    {
        return current($this->items);
    }

    /**
     * Advance the internal list pointer to the next item.
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function next()
    {
        next($this->items);
    }

    /**
     * Reset the internal list pointer to position 0.
     *
     * @see http://php.net/manual/en/class.iterator.php
     */
    public function rewind()
    {
        reset($this->items);
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

        return isset($this->items[$key]);
    }

    // Interface - CollectionInterface

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        if (empty($keys)) {
            return $this->items;
        }

        return array_filter($this->items, function ($key) use ($keys) {
            return in_array($key, $keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function withItem($key, $item)
    {
        if ($this->getItem($key) === $item) {
            return $this;
        }

        $copy = clone $this;
        $copy->items[$key] = $item;
        $this->guardConstraints($copy->items);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function withItems(array $items)
    {
        $copied_items = array_merge($this->items, $items);
        $this->guardConstraints($copied_items);

        $copy = clone $this;
        $copy->items = $copied_items;

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutItem($item)
    {
        if ($key = $this->getKey($item)) {
            $copy = clone $this;
            unset($copy->items[$key]);
            return $copy;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutItems(array $items = [])
    {
        $copied_items = array_diff($this->items, $items);
        if (count($copied_items) === $this->getSize()) {
            return $this;
        }
        $copy = clone $this;
        $copy->items = $copied_items;

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
    public function getKey($item)
    {
        $keys = array_keys($this->items, $item, true);

        return count($keys) > 0 ? $keys[0] : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys()
    {
        return array_keys($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($item)
    {
        return $this->getKey($item) !== false;
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
        $filtered_items = array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH);
        if (count($filtered_items) === $this->getSize()) {
            return $this;
        }
        $copy = clone $this;
        $copy->items = $filtered_items;

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Closure $callback)
    {
        $mapped_items = array_map($callback, $this->items);
        $this->guardConstraints($mapped_items);

        $copy = clone $this;
        $copy->items = $mapped_items;

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_map(static function ($item) {
            if (is_callable([ $item, 'toArray' ])) {
                $item = $item->toArray();
            }
            return $item;
        }, $this->items);
    }

    /**
     * Make sure that the given items ahere to the constraints that apply to a specific collection.
     *
     * @param mixed[] $items
     */
    protected function guardConstraints(array $items)
    {
        if (!$this instanceof UniqueItemInterface) {
            return;
        }
        foreach ($items as $item) {
            $found_keys = array_keys($items, $item, true);
            if (count($found_keys) > 1) {
                throw new Exception('Items within this collection must be unique.');
            }
        }
    }
}
