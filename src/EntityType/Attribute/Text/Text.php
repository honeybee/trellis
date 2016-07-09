<?php

namespace Trellis\EntityType\Attribute\Text;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Text implements ValueInterface
{
    use NativeEqualsComparison;

    const NIL = '';

    /**
     * @var string $value
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value = self::NIL)
    {
        Assertion::string($value, 'Text may only be constructed from string.');

        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->value === self::NIL;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->value;
    }
}
