<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectInterface;
use Trellis\Assert\Assertion;

final class Boolean implements ValueObjectInterface
{
    public const EMPTY = false;

    /**
     * @var bool $boolean
     */
    private $boolean;

    /**
     * @param bool $boolean
     */
    public function __construct(bool $boolean = self::EMPTY)
    {
        $this->boolean = $boolean;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        Assertion::isInstanceOf($other_value, Boolean::CLASS);
        return $this->toNative() === $other_value->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->boolean === self::EMPTY;
    }

    /**
     * @return bool
     */
    public function toNative(): bool
    {
        return $this->boolean;
    }

    /**
     * @return bool
     */
    public function isTrue(): bool
    {
        return $this->boolean === true;
    }

    /**
     * @return bool
     */
    public function isFalse(): bool
    {
        return $this->boolean === false;
    }

    /**
     * @return Boolean
     */
    public function negate(): Boolean
    {
        $clone = clone $this;
        $clone->boolean = !$this->boolean;
        return $clone;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->boolean ? "true" : "false";
    }
}
