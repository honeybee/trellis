<?php

namespace Trellis\EntityType\Attribute\TextList;

use Trellis\Collection\TypedList;
use Trellis\EntityType\Attribute\Text\Text;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class TextList extends TypedList implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @param string[] $texts
     *
     * @return TextList
     */
    public static function fromArray(array $texts)
    {
        $text_values = [];
        foreach ($texts as $text) {
            $text_values[] = new Text($text);
        }

        return new static($text_values);
    }

    /**
     * @param Text[] $texts
     */
    public function __construct(array $texts = [])
    {
        parent::__construct(Text::CLASS, $texts);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_map(static function ($item) {
            return $item->toNative();
        }, $this->items);
    }
}
