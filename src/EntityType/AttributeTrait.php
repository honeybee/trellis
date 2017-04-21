<?php

namespace Trellis\EntityType;

use Trellis\EntityType\Path\TypePath;
use Trellis\EntityType\Path\TypePathPart;

trait AttributeTrait
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var EntityTypeInterface
     */
    private $entityType;

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
    public function getEntityType(): EntityTypeInterface
    {
        return $this->entityType;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?AttributeInterface
    {
        return $this->getEntityType()->getParentAttribute();
    }
}
