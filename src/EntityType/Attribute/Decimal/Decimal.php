<?php

namespace Trellis\EntityType\Attribute\Decimal;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Decimal implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @var float $decimal
     */
    private $decimal;

    /**
     * @param float $decimal
     */
    public function __construct($decimal = null)
    {
        Assertion::nullOrFloat($decimal, 'Decimal may only be constructed from float or null.');

        $this->decimal = $decimal;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->decimal === null;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->decimal;
    }
}
