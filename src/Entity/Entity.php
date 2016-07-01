<?php

namespace Trellis\Entity;

use Trellis\Path\ValuePathParser;
use Trellis\Value\Nil;
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
     * @param ValuePathParser $path_parser
     */
    protected $path_parser;

    /**
     * @param EntityTypeInterface $type
     * @param mixed[] $data
     */
    public function __construct(EntityTypeInterface $type, array $data = [], EntityInterface $parent = null)
    {
        $this->type = $type;
        $this->parent = $parent;
        $this->value_map = new ValueMap($type, $data);
        $this->path_parser = ValuePathParser::create();
    }

    /**
     * {@inheritdoc}
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function parent()
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function root()
    {
        $tmp_parent = $this->parent();
        $root = $tmp_parent;

        while ($tmp_parent) {
            $root = $tmp_parent;
            $tmp_parent = $tmp_parent->parent();
        }

        return $root;
    }

    /**
     * {@inheritdoc}
     */
    public function get($value_path)
    {
        $paths = is_array($value_path) ? $value_path : [ $value_path ];
        $values = [];

        foreach ($paths as $path) {
            if (mb_strpos($path, '.')) {
                $values[$path] = $this->evaluatePath($path);
                continue;
            }

            $attribute = $this->type()->getAttribute($path);
            if ($this->value_map->hasKey($attribute->getName())) {
                $values[$path] = $this->value_map[$attribute->getName()];
            }
        }

        return is_array($value_path) ? $values : $values[$value_path];
    }

    /**
     * {@inheritdoc}
     */
    public function has($attribute_name)
    {
        if (!$this->value_map->hasKey($attribute_name)) {
            throw new Exception("Attribute '$attribute_name' has not known to the entity's value-map. ");
        }
        return !$this->value_map->getItem($attribute_name)->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function collateChildren(Closure $criteria, $recursive = true)
    {
        $entity_map = new EntityMap;
        $nested_attribute_types = [ EmbeddedEntityListAttribute::CLASS, EntityReferenceListAttribute::CLASS ];

        foreach ($this->type()->getAttributesByType($nested_attribute_types) as $attribute) {
            foreach ($this->get($attribute->getName()) as $child_entity) {
                if ($criteria($child_entity)) {
                    $entity_map = $entity_map->withItem($child_entity->toValuePat(), $child_entity);
                }
                if ($recursive) {
                    $entity_map = $entity_map->append($child_entity->collateChildren($criteria));
                }
            }
        }

        return $entity_map;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(EntityInterface $other_entity)
    {
        if ($other_entity->type() !== $this->type()) {
            return false;
        }

        foreach ($this->value_map as $attribute_name => $value) {
            if (!$value->isEqualTo($other_entity->get($attribute_name))) {
                return false;
            }
        }

        return true;
    }

   /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $attribute_values = [ self::OBJECT_TYPE => $this->type()->getPrefix() ];
        foreach ($this->value_map as $attribute_name => $value) {
            $attribute_values[$attribute_name] = $value->toNative();
        }

        return $attribute_values;
    }

    /**
     * {@inheritdoc}
     */
    public function toValuePath()
    {
        $parent_entity = $this->parent();
        $current_entity = $this;

        $path_parts = [];
        while ($parent_entity) {
            $parent_attr_name = $current_entity->type()->getParentAttribute()->getName();
            $entity_list = $parent_entity->get($parent_attr_name);
            array_push($path_parts, $entity_list->getKey($current_entity), $parent_attr_name);
            $current_entity = $parent_entity;
            $parent_entity = $parent_entity->parent();
        }
        $value_path = new ValuePath($path_parts);

        return (string)$value_path->reverse();
    }

    /**
     * @return mixed[]
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

    /**
     * Evaluates the given value_path and returns the corresponding entity or value.
     *
     * @param string $value_path
     *
     * @return ValueInterface|EntityInterface
     */
    protected function evaluatePath($value_path)
    {
        $value = null;
        $entity = $this;

        foreach ($this->path_parser->parse($value_path) as $path_part) {
            $value = $entity->get($path_part->getAttributeName());
            if ($path_part->hasPosition()) {
                $entity = $value->getItem($path_part->getPosition());
                $value = $entity;
            }
        }

        return $value;
    }
}
