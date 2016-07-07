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
     * @param ValueMap $other
     *
     * @return ValueMap
     */
    public function diff(ValueMap $other)
    {
        $this->guardTypeCompatibility($other);

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

    /**
     * @param ValueMap $other
     *
     * @return mixed[]
     */
    public function asDiffArray(ValueMap $other)
    {
        $this->guardTypeCompatibility($other);

        $diffs = [];
        foreach ($this->items as $attribute_name => $value) {
            $other_value = $other->getItem($attribute_name);
            if ($value instanceof EntityList) {
                $list_diff = $this->buildEntityListDiff($value, $other_value);
                if (!empty($list_diff)) {
                    $diffs[$attribute_name] = $list_diff;
                }
                continue;
            }
            if (!$value->isEqualTo($other_value)) {
                $diffs[$attribute_name] = $value->toNative();
            }
        }

        return $diffs;
    }

    /**
     * @param ValueInterface $lefthand_val
     * @param ValueInterface $righthand_val
     *
     * @return mixed[]
     */
    protected function buildEntityListDiff(ValueInterface $lefthand_val, ValueInterface $righthand_val)
    {
        $diff = [];
        foreach ($lefthand_val->diff($righthand_val) as $pos => $entity) {
            $other_entity = $righthand_val->getItem($pos);
            if ($other_entity) {
                $diff[$pos] = $entity->diff($other_entity, true);
            } else {
                $diff[$pos] = $entity->toArray();
            }
        }

        return $diff;
    }

    /**
     * @param ValueMap $other
     */
    protected function guardTypeCompatibility(ValueMap $other)
    {
        if ($other->parent->type() !== $this->parent->type()) {
            throw new Exception("May only diff ValueMaps of the same entity-type.");
        }
    }
}
