<?php

namespace Trellis\ValueObject;

use Trellis\Assert\Assertion;

final class Integer implements ValueObjectInterface
{
    /**
     * @var null
     */
    private const NIL = null;

    /**
     * @var int
     */
    private $intVal;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeValue, array $context = [])
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
        Assertion::isInstanceOf($otherValue, static::class);
        return $this->toNative() === $otherValue->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->intVal === self::NIL;
    }

    /**
     * @return null|int
     */
    public function toNative(): ?int
    {
        return $this->intVal;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->isEmpty() ? "null" : (string)$this->intVal;
    }

    /**
     * @param int|null $intVal
     */
    private function __construct(?int $intVal)
    {
        $this->intVal = $intVal;
    }
}
