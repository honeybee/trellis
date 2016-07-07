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
        foreach ($parent->getEntityType()->getAttributes() as $key => $attribute) {
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
            $attribute = $this->parent->getEntityType()->getAttribute($key);
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
                $attribute = $this->parent->getEntityType()->getAttribute($key);
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
    public function diff(ValueMap $value_map)
    {
        $this->guardTypeCompatibility($value_map);

        $different_values = [];
        foreach ($this->items as $attribute_name => $lefthand_value) {
            $righthand_value = $value_map->getItem($attribute_name);
            if ($lefthand_value instanceof EntityList) {
                $list_diff = $lefthand_value->diff($righthand_value);
                if ($list_diff->getSize() > 0) {
                    $different_values[$attribute_name] = $list_diff;
                }
                continue;
            }
            if (!$lefthand_value->isEqualTo($value_map->getItem($attribute_name))) {
                $different_values[$attribute_name] = $lefthand_value;
            }
        }

        $copy = clone $this;
        $copy->items = $different_values;

        return $copy;
    }

    /**
     * @param ValueMap $other
     *
     * @return mixed[]
     */
    public function diffAsArray(ValueMap $value_map)
    {
        $this->guardTypeCompatibility($value_map);

        $diff_array = [];
        foreach ($this->items as $attribute_name => $lefthand_value) {
            $righthand_value = $value_map->getItem($attribute_name);
            if ($lefthand_value instanceof EntityList) {
                $list_diff = $this->calcEntityListDiff($lefthand_value, $righthand_value);
                if (!empty($list_diff)) {
                    $diff_array[$attribute_name] = $list_diff;
                }
                continue;
            }

            if (!$lefthand_value->isEqualTo($righthand_value)) {
                $diff_array[$attribute_name] = $lefthand_value->toNative();
            }
        }

        return $diff_array;
    }

    /**
     * @param EntityList $lefthand_list
     * @param EntityList $righthand_list
     *
     * @return mixed[]
     */
    protected function calcEntityListDiff(EntityList $lefthand_list, EntityList $righthand_list)
    {
        $list_diff = [];
        foreach ($lefthand_list->diff($righthand_list) as $pos => $lefthand_entity) {
            if ($righthand_entity = $righthand_list->getItem($pos)) {
                $list_diff[$pos] = $lefthand_entity->diff($righthand_entity, true);
                continue;
            }
            // there is no entity for the current $pos within the $righthand_list,
            // so the whole $lefthand_entity is considered as the diff.
            $list_diff[$pos] = $lefthand_entity->toArray();
        }

        return $list_diff;
    }

    /**
     * @param ValueMap $other
     */
    protected function guardTypeCompatibility(ValueMap $other)
    {
        if ($other->parent->getEntityType() !== $this->parent->getEntityType()) {
            throw new Exception("May only diff ValueMaps of the same entity-type.");
        }
    }
}
