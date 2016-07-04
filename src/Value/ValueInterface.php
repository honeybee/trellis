<?php

namespace Trellis\Value;

interface ValueInterface
{
    /**
     * @return boolean
     */
    public function isEqualTo(ValueInterface $other_value);

    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * @return mixed
     */
    public function toNative();
}
