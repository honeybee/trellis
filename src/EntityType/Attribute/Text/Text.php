<?php

namespace Trellis\EntityType\Attribute\Text;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Text implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @var string $text
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct($text = '')
    {
        Assertion::string($text, 'Text may only be constructed from string.');

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
