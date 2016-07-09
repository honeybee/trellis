<?php

namespace Trellis\EntityType\Attribute\Textarea;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Textarea implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @var string $textarea
     */
    private $textarea;

    /**
     * @param string $textarea
     */
    public function __construct($textarea = '')
    {
        Assertion::string($textarea, 'Textarea may only be constructed from string.');

        $this->textarea = $textarea;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->textarea);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->textarea;
    }
}
