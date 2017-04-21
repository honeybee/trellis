<?php

namespace Trellis\Entity;

use Ds\Map;
use Trellis\ValueObject\ValueObjectInterface;

final class ValueObjectMap implements \IteratorAggregate, \Countable
{
    /**
     * @var TypedEntityInterface $entity
     */
    private $entity;

    /**
     * @var Map $internalMap
     */
    private $internalMap;

    /**
     * @param TypedEntityInterface $entity
     * @param mixed[] $values
     */
    public function __construct(TypedEntityInterface $entity, array $values = [])
    {
        $this->entity = $entity;
        $this->internalMap = new Map;
        foreach ($entity->getEntityType()->getAttributes() as $attrName => $attribute) {
            $this->internalMap[$attrName] = $attribute->makeValue($values[$attrName] ?? null, $this->entity);
        }
    }

    /**
     * @param string $attrName
     * @return boolean
     */
    public function has(string $attrName): bool
    {
        return $this->internalMap->hasKey($attrName);
    }

    /**
     * @param string $attrName
     * @return ValueObjectInterface
     */
    public function get(string $attrName): ValueObjectInterface
    {
        return $this->internalMap->get($attrName);
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
        $clonedMap->internalMap[$attrName] = $attribute->makeValue($value, $clonedMap->entity);
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
            $clonedMap->internalMap[$attrName] = $attribute->makeValue($value, $clonedMap->entity);
        }
        return $clonedMap;
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return array_map(function (ValueObjectInterface $value) {
            return $value->toNative();
        }, $this->internalMap->toArray());
    }

    /**
     * @param ValueObjectMap $valueMap
     * @return ValueObjectMap
     */
    public function diff(ValueObjectMap $valueMap): ValueObjectMap
    {
        $clonedMap = clone $this;
        $clonedMap->internalMap = $this->internalMap->filter(
            function (string $attrName, ValueObjectInterface $value) use ($valueMap): bool {
                return !$value->equals($valueMap->get($attrName));
            }
        );
        return $clonedMap;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->internalMap);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->internalMap->isEmpty();
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        return $this->internalMap->getIterator();
    }

    public function __clone()
    {
        $this->internalMap = new Map($this->internalMap->toArray());
    }
}
