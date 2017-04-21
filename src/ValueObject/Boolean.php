<?php

namespace Trellis\ValueObject;

use Trellis\Assert\Assertion;

final class Boolean implements ValueObjectInterface
{
    /**
     * @var bool
     */
    private $boolVal;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeValue, array $context = [])
    {
        return new static($nativeValue);
    }

    /**
     * {@inheritdoc}
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(false);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $otherValue): bool
    {
        Assertion::isInstanceOf($otherValue, Boolean::class);
        return $this->toNative() === $otherValue->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->isFalse();
    }

    /**
     * @return bool
     */
    public function toNative(): bool
    {
        return $this->boolVal;
    }

    /**
     * @return bool
     */
    public function isTrue(): bool
    {
        return $this->boolVal === true;
    }

    /**
     * @return bool
     */
    public function isFalse(): bool
    {
        return $this->boolVal === false;
    }

    /**
     * @return Boolean
     */
    public function negate(): Boolean
    {
        $clone = clone $this;
        $clone->boolVal = !$this->boolVal;
        return $clone;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->boolVal ? "true" : "false";
    }

    /**
     * @param bool $boolVal
     */
    private function __construct(bool $boolVal)
    {
        $this->boolVal = $boolVal;
    }
}
