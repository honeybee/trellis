<?php

namespace Trellis\Entity;

use Trellis\Attribute\AttributeMap;
use Trellis\Exception;
use Trellis\Path\TypePathParser;
use Trellis\Path\TypePathPart;

abstract class EntityType implements EntityTypeInterface
{
    /**
     * @var string $name Holds the type's name.
     */
    protected $name;

    /**
     * @var AttributeInterface $parent_attribute Holds a reference to the parent_attribute type, if there is one.
     */
    protected $parent_attribute;

    /**
     * @var AttributeMap $attribute_map Holds the type's attribute map.
     */
    protected $attribute_map;

    /**
     * @var string $prefix Holds the type's prefix.
     */
    protected $prefix;

    /**
     * Holds the type's prefix.
     *
     * @var string $prefix
     */
    protected $path_parser;

    /**
     * @param string $name
     * @param AttributeInterface[] $attributes
     * @param AttributeInterface $parent_attribute
     */
    public function __construct($name, array $attributes = [], AttributeInterface $parent_attribute = null)
    {
        $this->name = $name;
        $this->path_parser = TypePathParser::create();
        $this->parent_attribute = $parent_attribute;
        $this->attribute_map = $this->getDefaultAttributes()->append(new AttributeMap($attributes));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootType()
    {
        $parent = null;

        $next_parent = $this->getParent();
        while ($next_parent) {
            $parent = $next_parent;
            $next_parent = $parent->getParent();
        }

        return $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentAttribute()
    {
        return $this->parent_attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->hasParent() ? $this->getParentAttribute()->getType() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent()
    {
        return $this->getParentAttribute() instanceof AttributeInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot()
    {
        return !$this->hasParent();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        if (!$this->prefix) {
            if (ctype_lower($this->getName())) {
                $this->prefix = $this->getName();
            } else {
                $this->prefix = mb_strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $this->getName()));
            }
        }

        return $this->prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute($type_path)
    {
        if (mb_strpos($type_path, '.')) {
            try {
                return $this->getAttributeByPath($type_path) !== null;
            } catch (Exception $error) {
                return false;
            }
        }

        return isset($this->attribute_map[$type_path]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($type_path)
    {
        if (mb_strpos($type_path, '.')) {
            return $this->evaluatePath($type_path);
        }
        if (!isset($this->attribute_map[$type_path])) {
            throw new Exception("Attribute '$type_path' does not exist");
        }

        return $this->attribute_map[$type_path];
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(array $type_paths = [])
    {
        $attributes = [];
        foreach ($type_paths as $type_path) {
            $attributes[$type_path] = $this->getAttribute($type_path);
        }

        return empty($type_paths) ? $this->attribute_map : new AttributeMap($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesByType(array $attribute_types = [])
    {
        return $this->attribute_map->filter(function ($attribute) use ($attribute_types) {
            return in_array(get_class($attribute), $attribute_types);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function collateAttributes(Closure $filter, $recursive = true)
    {
        $mirrored_attributes = new AttributeMap;
        foreach ($this->attribute_map as $attribute_name => $attribute) {
            if ($filter($attribute) === true) {
                $mirrored_attributes->setItem($attribute->getPath(), $attribute);
            }
            if ($recursive && $attribute instanceof EmbeddedEntityListAttribute) {
                foreach ($attribute->getEmbeddedEntityTypeMap() as $embedded_type) {
                    $mirrored_attributes->append($embedded_type->collateAttributes($filter, $recursive));
                }
            }
        }

        return $mirrored_attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAttributes()
    {
        return new AttributeMap;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAttributeNames()
    {
        return $this->getDefaultAttributes()->getKeys();
    }

    /**
     * {@inheritdoc}
     */
    public function createEntity(array $data = [], EntityInterface $parent_attribute = null)
    {
        $implementor = $this->getEntityImplementor();
        if (!class_exists($implementor, true)) {
            throw new Exception(
                "Unable to resolve the given entity implementor '$implementor' upon entity creation request."
            );
        }

        return new $implementor($this, $data, $parent_attribute);
    }

    /**
     * {@inheritdoc}
     */
    protected function evaluatePath($type_path)
    {
        $attribute = null;
        $entity_type = $this;

        foreach ($this->path_parser->parse($type_path) as $path_part) {
            $attribute = $entity_type->getAttribute($path_part->getAttributeName());
            if ($path_part->hasType()) {
                $entity_type = $attribute->getEmbeddedTypeByName($path_part->getType());
            }
        }

        return $attribute;
    }
}
