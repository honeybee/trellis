<?php

namespace Trellis\EntityType\Attribute;

use Trellis\EntityType\Attribute\EntityList\EntityListAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\EntityType\Path\TypePath;
use Trellis\EntityType\Path\TypePathPart;

abstract class Attribute implements AttributeInterface
{
    /**
     * @var string $name Holds the attribute's name.
     */
    protected $name;

    /**
     * @var EntityTypeInterface $entity_type Holds a reference to the attribute's entity_type.
     */
    protected $entity_type;

    /**
     * @var mixed[] $options
     */
    protected $options;

    /**
     * @param string $name
     * @param EntityTypeInterface $entity_type
     * @param mixed[] $options
     */
    public function __construct($name, EntityTypeInterface $entity_type, array $options = [])
    {
        $this->name = $name;
        $this->entity_type = $entity_type;
        $this->options = new Options($options);
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
    public function getEntityType()
    {
        return $this->entity_type;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->getEntityType()->getParent();
    }

    /**
     * {@inheritdoc}
     */
    public function toTypePath()
    {
        $current_attribute = $this->getParent();
        $current_type = $this->getEntityType();
        $path_leaf = new TypePathPart($this->getName());

        $type_path = new TypePath([ $path_leaf ]);
        while ($current_attribute instanceof EntityListAttribute) {
            $type_path = $type_path->push(new TypePathPart($current_attribute->getName(), $current_type->getPrefix()));
            $current_attribute = $current_attribute->getParent();
            $current_type = $current_attribute->getEntityType();
        }
        $type_path = $type_path->getSize() > 1 ? $type_path->reverse() : $type_path;

        return  (string)$type_path;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootEntityType()
    {
        $root_type = $this->getEntityType()->getRootType();

        return $root_type ? $root_type : $this->getEntityType();
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
}
