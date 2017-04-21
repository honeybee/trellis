<?php

namespace Trellis\ValueObject;

use Trellis\Assert\Assertion;

final class Decimal implements ValueObjectInterface
{
    /**
     * @var null
     */
    private const NIL = null;

    /**
     * @var float
     */
    private $floatVal;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeValue): ValueObjectInterface
    {
        return $nativeValue ? new static($nativeValue) : self::makeEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(self::NIL);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $otherValue): bool
    {
        Assertion::isInstanceOf($otherValue, Decimal::class);
        return $this->toNative() === $otherValue->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->floatVal === self::NIL;
    }

    /**
     * @return null|float
     */
    public function toNative(): ?float
    {
        return $this->floatVal;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->isEmpty() ? "null" : (string)$this->floatVal;
    }

    /**
     * @param float|null $floatVal
     */
    private function __construct(?float $floatVal)
    {
        Assertion::nullOrFloat($floatVal, "Trying to create floatVal from invalid floatVal.");
        $this->floatVal = $floatVal;
    }
}
