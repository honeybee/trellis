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
    public static function fromArray(array $entityAsArray): EntityInterface
    {
        $entityType = $entityAsArray["@type"];
        Assertion::isInstanceOf($entityType, EntityTypeInterface::class);
        $parent = null;
        if (isset($entityAsArray["@parent"])) {
            $parent = $entityAsArray["@parent"];
            Assertion::isInstanceOf($parent, TypedEntityInterface::class);
            unset($entityAsArray["@parent"]);
        }
        return new static($entityType, $entityAsArray, $parent);
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
    public function toArray(): array
    {
        $entityState = $this->valueObjectMap->toArray();
        $entityState[self::ENTITY_TYPE] = $this->getEntityType()->getPrefix();
        return $entityState;
    }

    /**
     * @param EntityTypeInterface $type
     * @param mixed[] $values
     * @param null|TypedEntityInterface $parent
     */
    protected function __construct(EntityTypeInterface $type, array $values = [], TypedEntityInterface $parent = null)
    {
        $this->type = $type;
        $this->parent = $parent;
        $this->valueObjectMap = new ValueObjectMap($this, $values);
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
