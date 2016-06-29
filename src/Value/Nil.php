<?php

namespace Trellis\Value;

class Nil implements ValueInterface
{
    public function isEqualTo(ValueInterface $other_value)
    {
        return $other_value instanceof Nil;
    }

    public function toNative()
    {
        return null;
    }
}
