<?php

namespace Trellis\Runtime\Entity;

use Trellis\Common\Collection\CollectionChangedEvent;
use Trellis\Common\Collection\MandatoryKeyInterface;
use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueKeyInterface;
use Trellis\Common\Collection\UniqueValueInterface;
use Trellis\Common\Error\RuntimeException;

class EntityMap extends TypedMap implements UniqueKeyInterface, UniqueValueInterface
{
    /**
     * Initializes the map with the provided entity identifiers as keys
     *
     * @param array $entities
     */
    public function __construct(array $entities = [])
    {
        parent::__construct([]);
        foreach ($entities as $entity) {
            $this->ensureValidItemType($entity);
            $this->setItem($entity->getIdentifier(), $entity);
        }
    }

    public function setItems(array $entities)
    {
        foreach ($entities as $entity) {
            $this->setItem($entity->getIdentifier(), $entity);
        }
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

    /**
     * Return the key for the given item.
     *
     * If you wish to receive all keys, set the '$return_all' parameter to true.
     *
     * @param mixed $item
     * @param boolean $return_all
     *
     * @return mixed Returns the key of the item, an array of all keys or false, if the item is not present.
     */
    public function getKey($item, $return_all = false)
    {
        if ($return_all === true) {
            return $this->getKeys();
        }

        if ($this->hasKey($item->getIdentifier())) {
            return $item->getIdentifier();
        }

        return false;
    }

    /**
     * Returns the EntityInterface interface-name to the TypeMap parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return EntityInterface::CLASS;
    }
}
