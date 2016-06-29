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
    public function push($value)
    {
        return $this->withValue($this->getSize(), $value);
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
        array_pop($copy->values);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo($pos, $value)
    {
        $cur_pos = $this->getKey($value);
        if (!$this->hasValue($value) || $pos === $cur_pos) {
            return $this;
        }
        $copy = clone $this;
        array_splice($copy->values, $pos, 0, array_splice($copy->values, $cur_pos, 1));

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function insertAt($pos, $value)
    {
        if ($this->hasValue($value) && $pos === $this->getKey($value)) {
            return $this;
        }
        $this->guardConstraints([ $value ]);

        $copy = clone $this;
        array_splice($copy->values, $pos, 0, [ $value ]);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function splice($pos, $length = 1, array $values = [])
    {
        $this->guardConstraints($values);

        $copy = clone $this;
        array_splice($copy->values, $pos, $length, $values);

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
        array_shift($copy->values);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function unshift($value)
    {
        return $this->insertAt(0, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirst()
    {
        if ($this->getSize() > 0) {
            return $this->values[0];
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
            return $this->values[$item_count - 1];
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
        $values = $collection->getValues();
        array_push($copy->values, ...$values);
        $this->guardConstraints($copy->values);

        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function reverse()
    {
        $copy = clone $this;
        $copy->values = array_reverse($copy->values);

        return $copy;
    }
}
