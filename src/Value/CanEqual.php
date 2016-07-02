<?php

namespace Trellis\Value;

use Trellis\Exception;

trait CanEqual
{
    /**
     * @param ValueInterface $other
     *
     * @return boolean
     */
    public function isEqualTo(ValueInterface $other)
    {
        if (!$other instanceof static) {
            throw new Exception(
                "Can only compare to value by the type of '".static::CLASS."' Given: ".get_class($other)
            );
        }

        return $this->toNative() === $other->toNative();
    }
}
