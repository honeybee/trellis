<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectInterface;
use Trellis\Assert\Assertion;

final class Decimal implements ValueObjectInterface
{
    public const EMPTY = null;

    /**
     * @var float $decimal
     */
    private $decimal;

    /**
     * @param float $decimal
     */
    public function __construct(float $decimal = self::EMPTY)
    {
        Assertion::nullOrFloat($decimal, "Trying to create decimal from invalid value.");
        $this->decimal = $decimal;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        Assertion::isInstanceOf($other_value, Decimal::CLASS);
        return $this->toNative() === $other_value->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->decimal === self::EMPTY;
    }

    /**
     * @return null|float
     */
    public function toNative(): ?float
    {
        return $this->decimal;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->isEmpty() ? "null" : (string)$this->decimal;
    }
}
