<?php

namespace Trellis\Attribute\Text;

use Assert\Assertion;
use Trellis\Value\ValueInterface;

class Text implements ValueInterface
{
    private $text;

    public function __construct($text = '')
    {
        Assertion::string($text, 'Text may only be constructed from string.');

        $this->text = $text;
    }

    public function isEqualTo(ValueInterface $other_value)
    {
        return $this->toNative() === $other_value->toNative();
    }

    public function toNative()
    {
        return $this->text;
    }
}
