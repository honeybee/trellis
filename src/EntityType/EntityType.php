<?php

namespace Trellis\EntityType;

use Trellis\EntityTypeInterface;
use Trellis\Error\InvalidType;
use Trellis\EntityType\Path\TypePathParser;

abstract class EntityType implements EntityTypeInterface
{
    /**
     * @var string $name Holds the type's name.
     */
    private $name;

    /**
     * @var Params $params
     */
    private $params;

    /**
     * @var AttributeInterface $parent_attribute Holds a reference to the parent_attribute type, if there is one.
     */
    private $parent_attribute;

    /**
     * @var AttributeMap $attribute_map Holds the type's attribute map.
     */
    private $attribute_map;

    /**
     * @var string $prefix Holds the type's prefix.
     */
    private $prefix;

    /**
     * Holds the type's prefix.
     *
     * @var TypePathParser $path_parser
     */
    private $path_parser;

    /**
     * @param string $name
     * @param AttributeMap|null $attributes
     * @param Params|null $params
     * @param AttributeInterface $parent_attribute
     */
    public function __construct(
        string $name,
        AttributeMap $attributes,
        Params $params = null,
        AttributeInterface $parent_attribute = null
    ) {
        $this->name = $name;
        $this->params = $params ?? new Params;
        $this->parent_attribute = $parent_attribute;
        $this->path_parser = TypePathParser::create();
        $this->prefix = mb_strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $name));
        $this->attribute_map = $attributes ?? new AttributeMap;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot(): EntityTypeInterface
    {
        $root = $this;
        $next_parent = $this->getParent();
        while ($next_parent) {
            $root = $next_parent;
            $next_parent = $root->getParent();
        }
        return $root;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentAttribute(): ?AttributeInterface
    {
        return $this->parent_attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?EntityTypeInterface
    {
        return $this->hasParent() ? $this->getParentAttribute()->getEntityType() : null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent(): bool
    {
        return !is_null($this->getParentAttribute());
    }

    /**
     * {@inheritdoc}
     */
    public function isRoot(): bool
    {
        return !$this->hasParent();
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute(string $type_path): bool
    {
        if (mb_strpos($type_path, '.')) {
            return $this->evaluatePath($type_path) !== null;
        }
        return $this->attribute_map->has($type_path);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(string $type_path): AttributeInterface
    {
        if (mb_strpos($type_path, '.')) {
            return $this->evaluatePath($type_path);
        }
        if (!$this->attribute_map->has($type_path)) {
            throw new InvalidType("Attribute '$type_path' does not exist");
        }
        return $this->attribute_map->get($type_path);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(array $type_paths = []): AttributeMap
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
    public function getOption(string $key, $default = null, bool $fluent = false)
    {
        return $this->params->get($key, $fluent) ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function hasOption(string $key): bool
    {
        return $this->params->has($key);
    }

    /**
     * @param string $type_path
     *
     * @return AttributeInterface
     */
    private function evaluatePath(string $type_path): AttributeInterface
    {
        $attribute = null;
        $entity_type = $this;
        foreach ($this->path_parser->parse($type_path) as $path_part) {
            /* @var \Trellis\EntityType\Attribute\EntityListAttribute $attribute */
            $attribute = $entity_type->getAttribute($path_part->getAttributeName());
            if ($path_part->hasType()) {
                $entity_type = $attribute->getEntityTypeMap()->get($path_part->getType());
            }
        }
        return $attribute;
    }
}
