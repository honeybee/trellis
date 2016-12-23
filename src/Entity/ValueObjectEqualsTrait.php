<?php

namespace Trellis\Entity;

use Trellis\Error\InvalidType;

trait ValueObjectEqualsTrait
{
    /**
     * @param ValueObjectInterface $other_value
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        if (!$other_value instanceof static) {
            throw new InvalidType(
                "Trying to compare two values of different types. ".
                "Expecting instance of ".static::CLASS.
                ", but instead an instance of ".get_class($other_value)." was given."
            );
        }
        return $this->toNative() === $other_value->toNative();
    }
}
