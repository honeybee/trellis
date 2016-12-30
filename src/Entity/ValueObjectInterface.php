<?php

namespace Trellis\Entity;

interface ValueObjectInterface
{
    /**
     * @param ValueObjectInterface $other_value
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $other_value): bool;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @return mixed
     */
    public function toNative();

    /**
     * @return string
     */
    public function __toString(): string;
}
