<?php

namespace Trellis\ValueObject;

use Trellis\MapsToNativeValueInterface;

interface ValueObjectInterface extends MapsToNativeValueInterface
{
    /**
     * @return ValueObjectInterface
     */
    public static function makeEmpty(): ValueObjectInterface;

    /**
     * @param ValueObjectInterface $otherValue
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $otherValue): bool;

    /**
     * @return bool
     */
    public function isEmpty(): bool;
}
