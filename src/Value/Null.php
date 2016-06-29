<?php

namespace Trellis\Value;

class Null implements ValueInterface
{
    public function isEqualTo(ValueInterface $other_value)
    {
        return $other_value instanceof Null;
    }

    public function toNative()
    {
        return null;
    }
}
