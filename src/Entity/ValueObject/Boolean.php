<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectInterface;
use Trellis\Error\Assert\Assertion;

final class Boolean implements ValueObjectInterface
{
    const EMPTY = false;

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
}
