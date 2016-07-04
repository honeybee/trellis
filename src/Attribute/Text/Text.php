<?php

namespace Trellis\Attribute\Text;

use Assert\Assertion;
use Trellis\Value\NativeEqualsComparison;
use Trellis\Value\ValueInterface;

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
