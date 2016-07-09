<?php

namespace Trellis\EntityType;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\AttributeMap;
use Trellis\EntityType\Path\TypePathParser;
use Trellis\Entity\EntityInterface;
use Trellis\Exception;

abstract class EntityType implements EntityTypeInterface
{
    /**
     * @var string $name Holds the type's name.
     */
    protected $name;

    /**
     * @var Options $options
     */
    protected $options;

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
    public function __construct(
        $name,
        array $attributes = [],
        array $options = [],
        AttributeInterface $parent_attribute = null
    ) {
        $this->name = $name;
        $this->options = new Options($options);
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
    public function getRoot()
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
        return $this->hasParent() ? $this->getParentAttribute()->getEntityType() : null;
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
    public function isRootType()
    {
        return !$this->hasParent();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        if (!$this->prefix) {
            $this->prefix = mb_strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $this->getName()));
        }

        return $this->prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute($type_path)
    {
        if (mb_strpos($type_path, '.')) {
            return $this->evaluatePath($type_path) !== null;
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
    public function getDefaultAttributes()
    {
        return new AttributeMap;
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
     * @param string $key
     * @param mixed $default
     * @param boolean $fluent
     *
     * @return mixed|Options
     */
    public function getOption($key, $default = null, $fluent = false)
    {
        return $this->options->get($key, $default, $fluent);
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
                $entity_type = $attribute->getEntityTypeMap()->byPrefix($path_part->getType());
            }
        }

        return $attribute;
    }
}
