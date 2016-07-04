<?php

namespace Trellis\EntityType\Attribute\Boolean;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Boolean implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @var bool $boolean
     */
    private $boolean;

    /**
     * @param bool $boolean
     */
    public function __construct($boolean = false)
    {
        Assertion::boolean($boolean, 'Boolean may only be constructed from bool values.');

        $this->boolean = $boolean;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return !$this->boolean;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->boolean;
    }
}
