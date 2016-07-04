<?php

namespace Trellis\Collection;

use Trellis\Exception;

class Map extends Collection implements MapInterface
{
    public function append(CollectionInterface $collection)
    {
        if (!$collection instanceof static) {
            throw new Exception(
                sprintf("Can only append collections of the same type %s", get_class($this))
            );
        }
        $items = array_merge($this->items, $collection->getItems());
        $this->guardConstraints($items);

        $copy = clone $this;
        $copy->items = $items;

        return $copy;
    }
}
