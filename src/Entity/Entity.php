<?php

namespace Trellis\Entity;

use Trellis\EntityInterface;
use Trellis\EntityTypeInterface;
use Trellis\Entity\Path\ValuePath;
use Trellis\Entity\Path\ValuePathParser;
use Trellis\Entity\Path\ValuePathPart;
use Trellis\Error\UnknownAttribute;
use Trellis\DomainEntityInterface;

abstract class Entity implements DomainEntityInterface
{
    /**
     * @var EntityTypeInterface $type Holds the entity's type.
     */
    private $type;

    /**
     * @var EntityInterface $parent Holds a reference to the parent entity, if there is one.
     */
    private $parent;

    /**
     * @var ValueObjectMap $value_object_map
     */
    private $value_object_map;

    /**
     * @param ValuePathParser $path_parser
     */
    private $path_parser;

    /**
     * @param EntityTypeInterface $type
     * @param mixed[] $data
     * @param null|DomainEntityInterface $parent
     */
    public function __construct(EntityTypeInterface $type, array $data = [], DomainEntityInterface $parent = null)
    {
        $this->type = $type;
        $this->parent = $parent;
        $this->value_object_map = new ValueObjectMap($this, $data);
        $this->path_parser = ValuePathParser::create();
    }

    /**
     * {@inheritdoc}
     */
    public function isSameAs(EntityInterface $entity): bool
    {
        /* @var DomainEntityInterface $this */
        return $this->getIdentity()->equals($entity->getIdentity());
    }

    /**
     * {@inheritdoc}
     */
    public function withValue(string $attribute_name, $value): DomainEntityInterface
    {
        $copy = clone $this;
        $copy->value_object_map = $this->value_object_map->withValue($attribute_name, $value);
        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function withValues(array $values): DomainEntityInterface
    {
        $copy = clone $this;
        $copy->value_object_map = $this->value_object_map->withValues($values);
        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueObjectMap(): ValueObjectMap
    {
        return $this->value_object_map;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $attribute_name): bool
    {
        if (!$this->value_object_map->has($attribute_name)) {
            throw new UnknownAttribute("Attribute '$attribute_name' is not known to the entity's value-map. ");
        }
        return !$this->value_object_map->get($attribute_name)->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $value_path): ValueObjectInterface
    {
        if (mb_strpos($value_path, '.')) {
            return $this->evaluatePath($value_path);
        }
        if (!$this->value_object_map->has($value_path)) {
            throw new UnknownAttribute("Attribute '$value_path' is unknown by type ".$this->getEntityType()->getName());
        }
        return $this->value_object_map->get($value_path);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityRoot(): DomainEntityInterface
    {
        $tmp_parent = $this->getEntityParent();
        $root = $tmp_parent;
        while ($tmp_parent) {
            $root = $tmp_parent;
            $tmp_parent = $tmp_parent->getEntityParent();
        }
        return $root ?? $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityParent(): ?DomainEntityInterface
    {
        return $this->parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType(): EntityTypeInterface
    {
        return $this->type;
    }

   /**
     * {@inheritdoc}
     */
    public function toNative(): array
    {
        $attribute_values = [ self::ENTITY_TYPE => $this->getEntityType()->getPrefix() ];
        foreach ($this->value_object_map as $attribute_name => $value) {
            $attribute_values[$attribute_name] = $value->toNative();
        }
        return $attribute_values;
    }

    /**
     * {@inheritdoc}
     */
    public function toPath(): string
    {
        $parent_entity = $this->getEntityParent();
        $current_entity = $this;
        $value_path = new ValuePath;
        while ($parent_entity) {
            $attribute_name = $current_entity->getEntityType()->getParentAttribute()->getName();
            $entity_pos = $parent_entity->get($attribute_name)->getPos($current_entity);
            $value_path = $value_path->push(new ValuePathPart($attribute_name, $entity_pos));
            $current_entity = $parent_entity;
            $parent_entity = $parent_entity->getEntityParent();
        }
        return (string)(count($value_path) > 1 ? $value_path->reverse() : $value_path);
    }

    /**
     * Evaluates the given value_path and returns the corresponding entity or value.
     *
     * @param string $value_path
     *
     * @return ValueObjectInterface
     */
    private function evaluatePath($value_path): ValueObjectInterface
    {
        $value = null;
        $entity = $this;
        foreach ($this->path_parser->parse($value_path) as $path_part) {
            $value = $entity->get($path_part->getAttributeName());
            if ($path_part->hasPosition()) {
                $entity = $value->get($path_part->getPosition());
                $value = $entity;
            }
        }
        return $value;
    }
}
