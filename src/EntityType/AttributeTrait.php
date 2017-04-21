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

    /**
     * {@inheritdoc}
     */
    public function toPath(): string
    {
        $currentAttribute = $this->getParent();
        $currentType = $this->getEntityType();
        $pathLeaf = new TypePathPart($this->getName());
        $typePath = new TypePath([ $pathLeaf ]);
        while ($currentAttribute) {
            $pathPart = new TypePathPart($currentAttribute->getName(), $currentType->getPrefix());
            $typePath = $typePath->push($pathPart);
            $currentAttribute = $currentAttribute->getParent();
            if ($currentAttribute) {
                $currentType = $currentAttribute->getEntityType();
            }
        }
        return (string)$typePath->reverse();
    }
}
