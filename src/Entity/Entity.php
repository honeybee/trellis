<?php

namespace Trellis\Entity;

use Trellis\Value\ValueMap;

abstract class Entity implements EntityInterface, \JsonSerializable
{
    /**
     * @var EntityTypeInterface $type Holds the entity's type.
     */
    protected $type;

    /**
     * @var EntityInterface $parent Holds a reference to the parent entity, if there is one.
     */
    protected $parent;

    /**
     * @var ValueMapInterface $value_map
     */
    protected $value_map;

    /**
     * @param EntityTypeInterface $type
     * @param array $data
     */
    public function __construct(EntityTypeInterface $type, array $data = [], EntityInterface $parent = null)
    {
        $this->type = $type;
        $this->parent = $parent;
        $this->value_map = new ValueMap($type, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        $tmp_parent = $this->getParent();
        $root = $tmp_parent;
        while ($tmp_parent) {
            $root = $tmp_parent;
            $tmp_parent = $tmp_parent->getParent();
        }

        return $root;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($attribute_name)
    {
        $attribute = $this->getType()->getAttribute($attribute_name);
        if (isset($this->value_map[$attribute->getName()])) {
            return $this->value_map[$attribute->getName()];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue($attribute_name)
    {
        $value_holder = $this->getValueHolderFor($attribute_name);

        return !$value_holder->isNull();
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(array $attribute_names = [])
    {
        if (!empty($attribute_names)) {
            return $this->value_map;
        }

        return $this->value_map->filter(function ($attribute_name, $value) use ($attribute_names) {
            return in_array($attribute_name, $attribute_names);
        });
    }

   /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $attribute_values = [ self::OBJECT_TYPE => $this->getType()->getPrefix() ];
        foreach ($this->value_map as $attribute_name => $value) {
            $attribute_values[$attribute_name] = $value->toNative();
        }

        return $attribute_values;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(EntityInterface $other_entity)
    {
        if ($other_entity->getType() !== $this->getType()) {
            return false;
        }

        foreach ($this->value_map as $attribute_name => $value) {
            if (!$value->isEqualTo($other_entity->getValue($attribute_name))) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function asEmbedPath()
    {
        $parent_entity = $this->getParent();
        if (!$parent_entity) {
            return '';
        }

        $path_parts = [];
        $current_entity = $this;
        while ($parent_entity) {
            $parent_attr_name = $current_entity->getType()->getParentAttribute()->getName();
            $path_parts[] = sprintf(
                '%s[%d]',
                $current_entity->getType()->getPrefix(),
                $parent_entity->getValue($parent_attr_name)->getKey($current_entity)
            );
            $path_parts[] = $parent_attr_name;
            if (!$parent_entity->getType()->isRoot()) {
                $path_parts[] = $parent_entity->getType()->getPrefix();
            }
            $current_entity = $parent_entity;
            $parent_entity = $parent_entity->getParent();
        }

        return implode('.', array_reverse($path_parts));
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function collateChildren(Closure $criteria, $recursive = true)
    {
        $entity_map = new EntityMap;
        $nested_attribute_types = [ EmbeddedEntityListAttribute::CLASS, EntityReferenceListAttribute::CLASS ];
        foreach ($this->getType()->getAttributesByType($nested_attribute_types) as $attribute) {
            foreach ($this->getValue($attribute->getName()) as $child_entity) {
                if ($criteria($child_entity)) {
                    $entity_map->setItem($child_entity->asEmbedPath(), $child_entity);
                }
                if ($recursive) {
                    $entity_map->append($child_entity->collateChildren($criteria));
                }
            }
        }
        return $entity_map;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        /**
         * TODO what about line separator and paragraph separator characters
         * which are valid JSON but invalid javascript when not expressed as
         * escape sequence (\u2028, \u2029) in strings?
         */
        return $this->toArray();
    }
}
