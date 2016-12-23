<?php

namespace Trellis\Entity\ValueObject;

use Assert\Assertion;
use Trellis\Entity\ValueObjectEqualsTrait;
use Trellis\Entity\ValueObjectInterface;

final class Decimal implements ValueObjectInterface
{
    use ValueObjectEqualsTrait;

    const EMPTY = null;

    /**
     * @var float $decimal
     */
    private $decimal;

    /**
     * @param float $decimal
     */
    public function __construct(float $decimal = self::EMPTY)
    {
        Assertion::nullOrFloat($decimal, 'Trying to create decimal from invalid value.');
        $this->decimal = $decimal;
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
    public function toNative(): float
    {
        return $this->decimal;
    }
}
