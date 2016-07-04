<?php

namespace Trellis\EntityType\Attribute\Integer;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Integer implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @var int $integer
     */
    private $integer;

    /**
     * @param AttributeInterface $attribute
     * @param int $integer
     */
    public function __construct($integer = null)
    {
        Assertion::nullOrInteger($integer, 'Integer(s) may only be constructed from integer or null.');

        $this->integer = $integer;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->integer === null;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->integer;
    }
}
