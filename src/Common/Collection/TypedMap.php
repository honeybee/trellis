<?php

namespace Trellis\Common\Collection;

use Trellis\Common\Error\InvalidTypeException;

abstract class TypedMap extends Map
{
    abstract protected function getItemImplementor();

    public function offsetSet($offset, $value)
    {
        $this->ensureValidItemType($value);

        parent::offsetSet($offset, $value);
    }

    protected function ensureValidItemType($item)
    {
        $implementor = $this->getItemImplementor();

        if (!$item instanceof $implementor) {
            throw new InvalidTypeException(
                sprintf(
                    "Items passed to the '%s' method must relate to '%s'."
                    . "%sAn instance of '%s' was given instead.",
                    __METHOD__,
                    $implementor,
                    PHP_EOL,
                    is_object($item) ? get_class($item) : gettype($item)
                )
            );
        }
    }
}
