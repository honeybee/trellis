<?php

namespace Trellis\Value;

interface ValueInterface
{
    public function isEqualTo(ValueInterface $other_value);

    public function isEmpty();

    public function toNative();
}
