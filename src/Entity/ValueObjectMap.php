<?php

namespace Trellis\Entity;

use Honeybee\Frames\TypedMapTrait;
use Trellis\ValueObject\ValueObjectInterface;

final class ValueObjectMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    /**
     * @var TypedEntityInterface $entity
     */
    private $entity;

    /**
     * @param TypedEntityInterface $entity
     * @param array $entityState
     * @return ValueObjectMap
     */
    public static function forEntity(TypedEntityInterface $entity, array $entityState = []): self
    {
        return new static($entity, $entityState);
    }

    /**
     * @param string $attrName
     * @param mixed $value
     * @return self
     */
    public function withValue(string $attrName, $value): self
    {
        $clonedMap = clone $this;
        $attribute = $this->entity->getEntityType()->getAttribute($attrName);
        $clonedMap->compositeMap[$attrName] = $attribute->makeValue($value, $clonedMap->entity);
        return $clonedMap;
    }

    /**
     * @param mixed[] $values
     * @return self
     */
    public function withValues(array $values): self
    {
        $clonedMap = clone $this;
        foreach ($values as $attrName => $value) {
            $attribute = $clonedMap->entity->getEntityType()->getAttribute($attrName);
            $clonedMap->compositeMap[$attrName] = $attribute->makeValue($value, $clonedMap->entity);
        }
        return $clonedMap;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this as $attributeName => $valueObject) {
            $array[$attributeName] = $valueObject->toNative();
        }
        return $array;
    }

    /**
     * @param ValueObjectMap $valueMap
     * @return ValueObjectMap
     */
    public function diff(ValueObjectMap $valueMap): ValueObjectMap
    {
        $clonedMap = clone $this;
        $clonedMap->compositeMap = $this->compositeMap->filter(
            function (string $attrName, ValueObjectInterface $value) use ($valueMap): bool {
                return !$value->equals($valueMap->get($attrName));
            }
        );
        return $clonedMap;
    }

    /**
     * @param TypedEntityInterface $entity
     * @param mixed[] $values
     */
    private function __construct(TypedEntityInterface $entity, array $values = [])
    {
        $this->entity = $entity;
        $valueObjects = [];
        foreach ($entity->getEntityType()->getAttributes() as $attrName => $attribute) {
            $valueObjects[$attrName] = $attribute->makeValue($values[$attrName] ?? null, $this->entity);
        }
        $this->init($valueObjects, ValueObjectInterface::class);
    }
}
