<?php

namespace Trellis\EntityType;

use Trellis\Error\InvalidType;
use Trellis\EntityType\Path\TypePathParser;

abstract class EntityType implements EntityTypeInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var AttributeInterface
     */
    private $parentAttribute;

    /**
     * @var AttributeMap
     */
    private $attributeMap;

    /**
     * @var string $prefix
     */
    private $prefix;

    /**
     * @var TypePathParser
     */
    private $pathParser;

    /**
     * @param string $name
     * @param AttributeInterface[] $attributes
     * @param AttributeInterface $parentAttribute
     */
    public function __construct(string $name, array $attributes, AttributeInterface $parentAttribute = null)
    {
        $this->name = $name;
        $this->parentAttribute = $parentAttribute;
        $this->pathParser = TypePathParser::create();
        $this->attributeMap = new AttributeMap($attributes);
        $this->prefix = mb_strtolower(preg_replace("/(.)([A-Z])/", "$1_$2", $name));
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
        $nextParent = $this->getParent();
        while ($nextParent) {
            $root = $nextParent;
            $nextParent = $root->getParent();
        }
        return $root;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentAttribute(): ?AttributeInterface
    {
        return $this->parentAttribute;
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
    public function hasAttribute(string $typePath): bool
    {
        if (mb_strpos($typePath, ".")) {
            return $this->evaluatePath($typePath) !== null;
        }
        return $this->attributeMap->has($typePath);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(string $typePath): AttributeInterface
    {
        if (mb_strpos($typePath, ".")) {
            return $this->evaluatePath($typePath);
        }
        if (!$this->attributeMap->has($typePath)) {
            throw new InvalidType("Attribute '$typePath' does not exist");
        }
        return $this->attributeMap->get($typePath);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes(array $typePaths = []): AttributeMap
    {
        $attributes = [];
        foreach ($typePaths as $typePath) {
            $attributes[] = $this->getAttribute($typePath);
        }
        return empty($typePaths) ? $this->attributeMap : new AttributeMap($attributes);
    }

    /**
     * @param string $typePath
     * @return AttributeInterface
     */
    private function evaluatePath(string $typePath): AttributeInterface
    {
        $attribute = null;
        $entityType = $this;
        foreach ($this->pathParser->parse($typePath) as $pathPart) {
            /* @var \Trellis\EntityType\Attribute\NestedEntityListAttribute $attribute */
            $attribute = $entityType->getAttribute($pathPart->getAttributeName());
            if ($pathPart->hasType()) {
                $entityType = $attribute->getAllowedTypes()->get($pathPart->getType());
            }
        }
        return $attribute;
    }
}
