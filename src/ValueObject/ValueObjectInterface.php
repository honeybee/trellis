<?php

namespace Trellis\ValueObject;

interface ValueObjectInterface
{
    /**
     * @param mixed $nativeValue
     * @return ValueObjectInterface
     */
    public static function fromNative($nativeValue): ValueObjectInterface;

    /**
     * @return mixed
     */
    public function toNative();

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
