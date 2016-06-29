<?php

namespace Trellis\Collection;

use Closure;
use Trellis\Exception;

/**
 * ItemList should actually be named List, but php has this as a reserved token (T_LIST)
 * to support list($what, $for) = $arr'.
 * Php, y U no CASE-sensitive?! (╯°□°）╯︵ ┻━┻)
 */
class ItemList extends Collection implements ListInterface
{
    /**
     * {@inheritdoc}
     */
    public function push($item)
    {
        return $this->withItem($this->getSize(), $item);
    }

    /**
     * {@inheritdoc}
     */
    public function pop()
    {
        if ($this->isEmpty()) {
            return $this;
        }
        $copy = clone $this;
        array_pop($copy->items);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo($pos, $item)
    {
        $cur_pos = $this->getKey($item);
        if (!$this->hasItem($item) || $pos === $cur_pos) {
            return $this;
        }
        $copy = clone $this;
        array_splice($copy->items, $pos, 0, array_splice($copy->items, $cur_pos, 1));

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function insertAt($pos, $item)
    {
        if ($this->hasItem($item) && $pos === $this->getKey($item)) {
            return $this;
        }
        $this->guardConstraints([ $item ]);

        $copy = clone $this;
        array_splice($copy->items, $pos, 0, [ $item ]);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function splice($pos, $length = 1, array $items = [])
    {
        $this->guardConstraints($items);

        $copy = clone $this;
        array_splice($copy->items, $pos, $length, $items);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function shift()
    {
        if ($this->isEmpty()) {
            return $this;
        }
        $copy = clone $this;
        array_shift($copy->items);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function unshift($item)
    {
        return $this->insertAt(0, $item);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirst()
    {
        if ($this->getSize() > 0) {
            return $this->items[0];
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getLast()
    {
        $item_count = $this->getSize();
        if ($item_count > 0) {
            return $this->items[$item_count - 1];
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function append(CollectionInterface $collection)
    {
        if (!$collection instanceof static) {
            throw new Exception(
                sprintf("Can only append collections of the same type %s", get_class($this))
            );
        }
        $copy = clone $this;
        $items = $collection->getItems();
        array_push($copy->items, ...$items);
        $this->guardConstraints($copy->items);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function reverse()
    {
        $copy = clone $this;
        $copy->items = array_reverse($copy->items);

        return $copy;
    }
}
