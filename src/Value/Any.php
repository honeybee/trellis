<?php

namespace Trellis\Value;

class Any implements ValueInterface
{
    private $value;

    public function __construct($native_value = null)
    {
        $this->value = $native_value;
    }

    public function isEqualTo(ValueInterface $other_value)
    {
        return $this->toNative() === $other_value->toNative();
    }

    public function toNative()
    {
        return $this->value;
    }
}
