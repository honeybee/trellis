<?php

namespace Trellis\Attribute;

use Trellis\Entity\EntityTypeInterface;
use Trellis\Path\TypePath;

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
     * @param string $name
     * @param EntityTypeInterface $entity_type
     */
    public function __construct($name, EntityTypeInterface $entity_type)
    {
        $this->name = $name;
        $this->entity_type = $entity_type;
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
        return $this->getType()->getParent();
    }

    /**
     * {@inheritdoc}
     */
    public function toTypePath()
    {
        $path_parts = [ $this->getName() ];
        $current_attribute = $this->getParent();
        $current_type = $this->getType();

        while ($current_attribute instanceof EmbeddedEntityListAttribute) {
            array_push($path_parts, $current_type->getPrefix(), $current_attribute->getName());
            $current_type = $current_attribute->getType();
            $current_attribute = $current_attribute->getParent();
        }
        $type_path = new TypePath($path_parts);

        return (string)$type_path->reverse();
    }

    /**
     * {@inheritdoc}
     */
    public function getRootEntityType()
    {
        $root_type = $attribute->getType()->getRoot();

        return $root_type ? $root_type : $attribute->getType();
    }
}
