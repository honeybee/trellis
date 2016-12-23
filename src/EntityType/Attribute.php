<?php

namespace Trellis\EntityType;

use Trellis\EntityTypeInterface;
use Trellis\EntityType\Path\TypePath;
use Trellis\EntityType\Path\TypePathPart;

abstract class Attribute implements AttributeInterface
{
    /**
     * @var string $name Holds the attribute's name.
     */
    private $name;

    /**
     * @var EntityTypeInterface $entity_type Holds a reference to the attribute's entity_type.
     */
    private $entity_type;

    /**
     * @var mixed[] $params
     */
    private $params;

    /**
     * @param string $name
     * @param EntityTypeInterface $entity_type
     * @param mixed[] $params
     */
    public function __construct(string $name, EntityTypeInterface $entity_type, array $params = [])
    {
        $this->name = $name;
        $this->entity_type = $entity_type;
        $this->params = new Params($params);
    }

    /**
     * Returns the name of the attribute.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the attribute's type.
     *
     * @return EntityTypeInterface
     */
    public function getEntityType(): EntityTypeInterface
    {
        return $this->entity_type;
    }

    /**
     * Returns the attribute's parent, if it has one.
     *
     * @return null|AttributeInterface
     */
    public function getParent(): ?AttributeInterface
    {
        return $this->getEntityType()->getParentAttribute();
    }

    /**
     * @param string $key
     * @param mixed $default
     * @param boolean $fluent
     *
     * @return mixed|Params
     */
    public function getOption(string $key, $default = null, bool $fluent = false)
    {
        return $this->params->get($key, $fluent) ?? $default;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasOption(string $key): bool
    {
        return $this->params->has($key);
    }

    /**
     * Returns attribute path of this attribute. Depending on this attribute
     * being part of an embedded entity this may look like this format:
     * {attribute_name}.{type_prefix}.{attribute_name}
     *
     * @return string attribute path of this attribute
     */
    public function toPath(): string
    {
        $current_attribute = $this->getParent();
        $current_type = $this->getEntityType();
        $path_leaf = new TypePathPart($this->getName());
        $type_path = new TypePath([ $path_leaf ]);
        while ($current_attribute) {
            $type_path = $type_path->push(
                new TypePathPart($current_attribute->getName(), $current_type->getPrefix())
            );
            if ($current_attribute = $current_attribute->getParent()) {
                $current_type = $current_attribute->getEntityType();
            }
        }
        return (string)(count($type_path) > 1 ? $type_path->reverse() : $type_path);
    }
}
