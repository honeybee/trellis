<?php

namespace Trellis\Attribute\Text;

use Assert\Assertion;
use Trellis\Attribute\AttributeInterface;
use Trellis\Value\CanEqual;
use Trellis\Value\HasAttribute;
use Trellis\Value\ValueInterface;

class Text implements ValueInterface
{
    use HasAttribute;
    use CanEqual;

    /**
     * @var string $text
     */
    private $text;

    /**
     * @param AttributeInterface $attribute
     * @param string $text
     */
    public function __construct(AttributeInterface $attribute, $text = '')
    {
        Assertion::string($text, 'Text may only be constructed from string.');

        $this->attribute = $attribute;
        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->text);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->text;
    }
}
