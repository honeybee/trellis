<?php

namespace Trellis\Attribute\TextList;

use Assert\Assertion;
use Trellis\Attribute\AttributeInterface;
use Trellis\Collection\TypedList;
use Trellis\Value\NativeEqualsComparison;
use Trellis\Value\HasAttribute;
use Trellis\Value\ValueInterface;

class TextList extends TypedList implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @param string[] $texts
     */
    public function __construct(array $texts = [])
    {
        Assertion::isArray($texts, 'TextList(s) may only be constructed from an array of strings.');

        parent::__construct('string', $texts);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->toArray();
    }
}
