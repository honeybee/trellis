<?php

namespace Trellis\EntityType\Attribute\ImageList;

use Trellis\Collection\TypedList;
use Trellis\EntityType\Attribute\Image\Image;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class ImageList extends TypedList implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @param mixed[] $images
     *
     * @return ImageList
     */
    public static function fromArray(array $images)
    {
        $image_values = [];
        foreach ($images as $image) {
            $image_values[] = Image::fromArray($image);
        }

        return new static($image_values);
    }

    /**
     * @param Image[] $images
     */
    public function __construct(array $images = [])
    {
        parent::__construct(Image::CLASS, $images);
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
