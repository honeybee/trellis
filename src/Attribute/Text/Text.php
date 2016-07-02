<?php

namespace Trellis\Attribute\Text;

use Assert\Assertion;
use Trellis\Attribute\AttributeInterface;
use Trellis\Value\HasAttribute;
use Trellis\Value\ValueInterface;

class Text implements ValueInterface
{
    use HasAttribute;

    private $text;

    public function __construct(AttributeInterface $attribute, $text = '')
    {
        Assertion::string($text, 'Text may only be constructed from string.');

        $this->attribute_name = $attribute;
        $this->text = $text;
    }

    public function isEqualTo(ValueInterface $other_value)
    {
        return $this->toNative() === $other_value->toNative();
    }

    public function isEmpty()
    {
        return empty($this->text);
    }

    public function toNative()
    {
        return $this->text;
    }
}
