<?php

namespace Trellis\EntityType\Attribute;

use Closure;
use Trellis\Collection\TypedMap;
use Trellis\Collection\UniqueItemInterface;
use Trellis\EntityType\Attribute\EntityList\EntityListAttribute;

class AttributeMap extends TypedMap
{
    /**
     * @param AttributeInterface[] $attributes
     */
    public function __construct(array $attributes = [])
    {
        $mapped_attributes = [];
        foreach ($attributes as $attribute) {
            $mapped_attributes[$attribute->getName()] = $attribute;
        }

        parent::__construct(AttributeInterface::CLASS, $mapped_attributes);
    }

    /**
     * Returns a map of path indexed attributes satisifed by the given criteria/callback predicate.
     *
     * @param Closure $criteria Returns a boolean for each element, that shall be contained within the resulting map.
     * @param boolean $recursive
     *
     * @return AttributeMap wth attribute_path => $attribute
     */
    public function collateAttributes(Closure $criteria, $recursive = true)
    {
        $attribute_map = new static;
        foreach ($this->items as $attribute_name => $attribute) {
            if ($criteria($attribute) === true) {
                $attribute_map = $attribute_map->withItem($attribute->getPath(), $attribute);
            }
            if ($recursive && $attribute instanceof EntityListAttribute) {
                foreach ($attribute->getEntityTypeTypeMap() as $entity_type) {
                    $attribute_map = $attribute_map->append($entity_type->collateAttributes($criteria));
                }
            }
        }

        return $attribute_map;
    }

    /**
     * Returns the type's attribute collection filter by a set of attribute classes-names.
     *
     * @param string[] $attribute_classes A list of attribute-classes to filter for.
     *
     * @return AttributeMap
     */
    public function byClassNames(array $attribute_classes = [])
    {
        return $this->attribute_map->filter(function ($attribute) use ($attribute_classes) {
            return in_array(get_class($attribute), $attribute_classes);
        });
    }
}
