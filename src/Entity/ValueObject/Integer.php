<?php

namespace Trellis\Entity\ValueObject;

use Assert\Assertion;
use Trellis\Entity\ValueObjectInterface;

final class Integer implements ValueObjectInterface
{
    const EMPTY = null;

    /**
     * @var int $integer
     */
    private $integer;

    /**
     * @param int $integer
     */
    public function __construct(int $integer = self::EMPTY)
    {
        $this->integer = $integer;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        Assertion::isInstanceOf($other_value, Integer::CLASS);
        return $this->toNative() === $other_value->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->integer === self::EMPTY;
    }

    /**
     * @return null|int
     */
    public function toNative(): ?int
    {
        return $this->integer;
    }
}
