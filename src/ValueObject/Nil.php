<?php

namespace Trellis\ValueObject;

final class Nil implements ValueObjectInterface
{
    public static function fromNative($nativeValue): ValueObjectInterface
    {
        return new static;
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        return new static;
    }

    /**
     * @param ValueObjectInterface $otherValue
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $otherValue): bool
    {
        return $otherValue instanceof Nil;
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

    private function __construct()
    {
    }
}
