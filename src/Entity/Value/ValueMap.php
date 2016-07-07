<?php

namespace Trellis\Entity\Value;

use Trellis\Collection\TypedMap;
use Trellis\EntityType\Attribute\EntityList\EntityList;
use Trellis\Entity\EntityInterface;
use Trellis\Exception;

class ValueMap extends TypedMap
{
    /**
     * @var EntityInterface $parent
     */
    protected $parent;

    /**
     * @param EntityInterface $parent
     * @param mixed[] $data
     */
    public function __construct(EntityInterface $parent, array $data = [])
    {
        $this->parent = $parent;

        $values = [];
        foreach ($parent->type()->getAttributes() as $key => $attribute) {
            $values[$key] = $attribute->createValue(
                $this->parent,
                array_key_exists($key, $data) ? $data[$key] : null
            );
        }

        parent::__construct(ValueInterface::CLASS, $values);
    }

    /**
     * {@inheritdoc}
     */
    public function withItem($key, $item)
    {
        if (!$item instanceof ValueInterface) {
            $attribute = $this->parent->type()->getAttribute($key);
            $item = $attribute->createValue($this->parent, $item);
        }

        return parent::withItem($key, $item);
    }

    /**
     * {@inheritdoc}
     */
    public function withItems(array $items)
    {
        $casted_items = [];
        foreach ($items as $key => $item) {
            if (!$item instanceof ValueInterface) {
                $attribute = $this->parent->type()->getAttribute($key);
                $casted_items[$key] = $attribute->createValue($this->parent, $item);
            } else {
                $casted_items[$key] = $item;
            }
        }

        return parent::withItems($casted_items);
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

    /**
     * @param mixed[] $data
     *
     * @return ValueMap
     */
    public function diffArray(array $data)
    {
        return $this->diff(new static($this->parent, $data));
    }

    /**
     * @param ValueMap $other
     *
     * @return ValueMap
     */
    public function diff(ValueMap $other)
    {
        if ($other->parent->type() !== $this->parent->type()) {
            throw new Exception("May only diff ValueMaps of the same entity-type.");
        }

        $diffs = [];
        foreach ($this->items as $attribute_name => $value) {
            if ($value instanceof EntityList) {
                $diff = $value->diff($other->getItem($attribute_name));
                if ($diff->getSize() > 0) {
                    $diffs[$attribute_name] = $diff;
                }
                continue;
            }

            if (!$value->isEqualTo($other->getItem($attribute_name))) {
                $diffs[$attribute_name] = $value;
            }
        }

        $copy = clone $this;
        $copy->items = $diffs;

        return $copy;
    }
}
