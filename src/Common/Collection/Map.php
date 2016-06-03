<?php

namespace Trellis\Common\Collection;

use Closure;
use Trellis\Common\Error\RuntimeException;

class Map extends Collection implements MapInterface
{
    public function __construct(array $items = [])
    {
        parent::__construct();

        $this->setItems($items);
    }

    /**
     * @see http://php.net/manual/en/class.arrayaccess.php
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            $removed_item = $this->items[$offset];
            unset($this->items[$offset]);
            $this->propagateCollectionChangedEvent(
                new CollectionChangedEvent($removed_item, CollectionChangedEvent::ITEM_REMOVED)
            );
        } elseif ($this instanceof MandatoryKeyInterface) {
            throw new RuntimeException('Item to be unset not found at key: ' . $offset);
        }
    }

    // CollectionInterface

    public function filter(Closure $callback)
    {
        $filtered_map = new static;

        foreach ($this->items as $key => $item) {
            if ($callback($item) === true) {
                $filtered_map->setItem($key, $item);
            }
        }

        return $filtered_map;
    }

    // MapInterface

    public function setItem($key, $item)
    {
        $this->offsetSet($key, $item);
    }

    public function setItems(array $items)
    {
        foreach ($items as $key => $item) {
            $this->setItem($key, $item);
        }
    }

    public function getKeys()
    {
        return array_keys($this->items);
    }

    public function getValues()
    {
        return array_values($this->items);
    }

    public function append(CollectionInterface $collection)
    {
        if (!$collection instanceof static) {
            throw new RuntimeException(
                sprintf('Can only append collections of the same type: ' . static::CLASS)
            );
        }

        foreach ($collection as $key => $item) {
            $this->setItem($key, $item);
        }

        return $this;
    }
}
