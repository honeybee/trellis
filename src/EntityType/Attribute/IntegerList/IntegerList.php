<?php

namespace Trellis\EntityType\Attribute\IntegerList;

use Trellis\Collection\TypedList;
use Trellis\EntityType\Attribute\Integer\Integer;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class IntegerList extends TypedList implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @param int[] $integers
     *
     * @return IntegerList
     */
    public static function fromArray(array $integers)
    {
        $integer_values = [];
        foreach ($integers as $integer) {
            $integer_values[] = new Integer($integer);
        }

        return new static($integer_values);
    }

    /**
     * @param Integer[] $integers
     */
    public function __construct(array $integers = [])
    {
        parent::__construct(Integer::CLASS, $integers);
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
