<?php

namespace Trellis\Entity;

use Trellis\Attribute\AttributeMap;
use Trellis\Exception;
use Trellis\Path\AttributePathPart;
use Trellis\Path\TrellisPathParser;

abstract class EntityType implements EntityTypeInterface
{
    /**
     * Holds the type's name.
     *
     * @var string $name
     */
    protected $name;

    /**
     * Holds a reference to the parent_attribute type, if there is one.
     *
     * @var AttributeInterface $parent_attribute;
     */
    protected $parent_attribute;

    /**
     * Holds the type's attribute map.
     *
     * @var AttributeMap $attribute_map
     */
    protected $attribute_map;

    /**
     * Holds the type's prefix.
     *
     * @var string $prefix
     */
    protected $prefix;

    protected $path_parser;

    /**
     * @param string $name
     * @param array $attributes
     * @param AttributeInterface $parent_attribute
     */
    public function __construct($name, array $attributes = [], AttributeInterface $parent_attribute = null)
    {
        $this->name = $name;
        $this->path_parser = TrellisPathParser::create();
        $this->parent_attribute = $parent_attribute;
        $this->attribute_map = $this->getDefaultAttributes()->withAttributesAdded(
            new AttributeMap($attributes)
        );
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
    public function getAttributes()
    {
        return $this->attribute_map;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributesByName(array $attribute_names = [])
    {
        return $this->attribute_map->filter(function ($attribute) use ($attribute_names) {
            return in_array($attribute->getName(), $attribute_names);
        });
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
    public function hasAttribute($attribute_name)
    {
        if (mb_strpos($attribute_name, '.')) {
            try {
                return $this->getAttributeByPath($attribute_name) !== null;
            } catch (Exception $error) {
                return false;
            }
        }

        return isset($this->attribute_map[$attribute_name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        if (mb_strpos($name, '.')) {
            return $this->getAttributeByPath($name);
        }

        if (!isset($this->attribute_map[$name])) {
            throw new Exception("Attribute '$name' does not exist");
        }

        return $this->attribute_map[$name];
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
    public function getDefaultAttributeNames()
    {
        return $this->getDefaultAttributes()->getKeys();
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
    public function getAttributeByPath($attribute_path)
    {
        $path_parts = $this->path_parser->parse($attribute_path);
        $attribute = null;
        $entity_type = $this;

        foreach ($path_parts as $path_part) {
            if (!$path_part instanceof AttributePathPart) {
                throw new Exception(
                    "Trellis-path error: Only ".AttributePathPart::CLASS." instances allowed for retrieving attributes."
                );
            }
            $attribute = $entity_type->getAttribute($path_part->getAttributeName());
            if ($path_part->hasType()) {
                $entity_type = $attribute->getEmbeddedTypeByName($path_part->getType());
            }
        }

        return $attribute;
    }
}
