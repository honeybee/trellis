<?php

namespace Trellis\Entity;

use Trellis\Assert\Assertion;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Entity\Path\ValuePath;
use Trellis\Entity\Path\ValuePathParser;
use Trellis\Entity\Path\ValuePathPart;
use Trellis\Error\UnknownAttribute;
use Trellis\ValueObject\NestedEntityList;
use Trellis\ValueObject\ValueObjectInterface;

abstract class Entity implements TypedEntityInterface
{
    /**
     * @var EntityTypeInterface
     */
    private $type;

    /**
     * @var EntityInterface
     */
    private $parent;

    /**
     * @var ValueObjectMap
     */
    private $valueObjectMap;

    /**
     * @param ValuePathParser
     */
    private $pathParser;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeState, array $context = [])
    {
        return new static($context["entity_type"], $nativeState ?? [], $context["parent"] ?? null);
    }

    /**
     * {@inheritdoc}
     */
    public function isSameAs(EntityInterface $entity): bool
    {
        Assertion::isInstanceOf($entity, static::class);
        return $this->getIdentity()->equals($entity->getIdentity());
    }

    /**
     * {@inheritdoc}
     */
    public function withValue(string $attributeName, $value): EntityInterface
    {
        $copy = clone $this;
        $copy->valueObjectMap = $this->valueObjectMap->withValue($attributeName, $value);
        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function withValues(array $values): EntityInterface
    {
        $copy = clone $this;
        $copy->valueObjectMap = $this->valueObjectMap->withValues($values);
        return $copy;
    }

    /**
     * {@inheritdoc}
     */
    public function getValueObjectMap(): ValueObjectMap
    {
        return $this->valueObjectMap;
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $attributeName): bool
    {
        if (!$this->valueObjectMap->has($attributeName)) {
            throw new UnknownAttribute("Attribute '$attributeName' is not known to the entity's value-map. ");
        }
        return !$this->valueObjectMap->get($attributeName)->isEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $valuePath): ValueObjectInterface
    {
        if (mb_strpos($valuePath, ".")) {
            return $this->evaluatePath($valuePath);
        }
        if (!$this->valueObjectMap->has($valuePath)) {
            throw new UnknownAttribute("Attribute '$valuePath' is unknown by type ".$this->getEntityType()->getName());
        }
        return $this->valueObjectMap->get($valuePath);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityRoot(): TypedEntityInterface
    {
        $tmpParent = $this->getEntityParent();
        $root = $tmpParent;
        while ($tmpParent) {
            $root = $tmpParent;
            $tmpParent = $tmpParent->getEntityParent();
        }
        return $root ?? $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityParent(): ?TypedEntityInterface
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
    public function toNative()
    {
        $entityState = [ self::ENTITY_TYPE => $this->getEntityType()->getPrefix() ];
        foreach ($this->valueObjectMap as $attributeName => $value) {
            $entityState[$attributeName] = $value->toNative();
        }
        return $entityState;
    }

    /**
     * @param EntityTypeInterface $type
     * @param mixed[] $data
     * @param null|TypedEntityInterface $parent
     */
    protected function __construct(EntityTypeInterface $type, array $data = [], TypedEntityInterface $parent = null)
    {
        $this->type = $type;
        $this->parent = $parent;
        $this->valueObjectMap = new ValueObjectMap($this, $data);
        $this->pathParser = ValuePathParser::create();
    }

    /**
     * Evaluates the given valuePath and returns the corresponding entity or value.
     * @param string $valuePath
     * @return ValueObjectInterface
     */
    private function evaluatePath($valuePath): ValueObjectInterface
    {
        $value = null;
        $entity = $this;
        foreach ($this->pathParser->parse($valuePath) as $pathPart) {
            /* @var TypedEntityInterface $value */
            $value = $entity->get($pathPart->getAttributeName());
            if ($pathPart->hasPosition()) {
                $entity = $value->get($pathPart->getPosition());
                $value = $entity;
            }
        }
        return $value;
    }
}
