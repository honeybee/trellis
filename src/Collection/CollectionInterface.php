<?php

namespace Trellis\Collection;

use Closure;

interface CollectionInterface extends \Iterator, \Countable, \ArrayAccess
{
    /**
     * Return a specific item from the collection for the given key.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function getItem($key);

    /**
     * Return a list of specific items from the collection for the given keys.
     *
     * @param mixed[] $keys
     *
     * @return mixed
     */
    public function getItems(array $keys = []);

    /**
     * Add the given key-item pair to the collection.
     *
     * @param mixed $key
     * @param mixed $item
     *
     * @return CollectionInterface
     */
    public function withItem($key, $item);

    /**
     * Adds the given key-items pairs to the collection.
     *
     * @param mixed[] $items
     *
     * @return CollectionInterface
     */
    public function withItems(array $items);

    /**
     * Remove the given item from the collection.
     *
     * @param mixed $item
     *
     * @return CollectionInterface
     */
    public function withoutItem($item);

    /**
     * Remove the given items from the collection.
     *
     * @param mixed[] $items
     *
     * @return CollectionInterface
     */
    public function withoutItems(array $items = []);

    /**
     * Tells if the collection has an item set for the given key.
     *
     * @param mixed $key
     *
     * @return boolean
     */
    public function hasKey($key);

    /**
     * Return the key for the given item.
     *
     * @param mixed $item
     *
     * @return mixed Returns false, if the item is not contained.
     */
    public function getKey($item);

    /**
     * Returns all keys of the collection.
     *
     * @return string[]|int[]
     */
    public function getKeys();

    /**
     * Tells if the collection contains the given item.
     *
     * @param mixed $item
     *
     * @return boolean
     */
    public function hasItem($item);

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
     * Returns an array representation of the collection's contained items.
     *
     * @return mixed[]
     */
    public function toArray();

    /**
     * Appends the given collection.
     *
     * @param CollectionInterface $collection
     *
     * @return CollectionInterface
     */
    public function append(CollectionInterface $collection);
}
