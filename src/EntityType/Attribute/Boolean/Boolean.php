<?php

namespace Trellis\EntityType\Attribute\Boolean;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Boolean implements ValueInterface
{
    use NativeEqualsComparison;

    const NIL = false;

    /**
     * @var bool $boolean
     */
    private $boolean;

    /**
     * @param bool $boolean
     */
    public function __construct($boolean = self::NIL)
    {
        Assertion::boolean($boolean, 'Boolean may only be constructed from bool values.');

        $this->boolean = $boolean;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->boolean === self::NIL;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->boolean;
    }

    /**
     * @return Boolean
     */
    public function negate()
    {
        return new static(!$this->boolean);
    }
}
