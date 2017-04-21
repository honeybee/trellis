<?php

namespace Trellis\ValueObject;

use Trellis\Assert\Assertion;

final class Text implements ValueObjectInterface
{
    private const NIL = "";

    /**
     * @var stringVal
     */
    private $stringVal;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeValue, array $context = [])
    {
        Assertion::nullOrString($nativeValue);
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
        Assertion::isInstanceOf($otherValue, Text::class);
        return $this->toNative() === $otherValue->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->stringVal === self::NIL;
    }

    /**
     * @return string
     */
    public function toNative(): string
    {
        return $this->stringVal;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return strlen($this->stringVal);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toNative();
    }

    /**
     * @param string $stringVal
     */
    private function __construct(string $stringVal)
    {
        $this->stringVal = $stringVal;
    }
}
