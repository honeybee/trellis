<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectInterface;

final class Nil implements ValueObjectInterface
{
    /**
     * @param ValueObjectInterface $other_value
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        return $other_value instanceof Nil;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function toNative()
    {
        return null;
    }

    public function __toString(): string
    {
        return "null";
    }
}
