<?php

namespace Trellis\Entity;

use Ds\Map;
use Trellis\DomainEntityInterface;

final class ValueObjectMap implements \IteratorAggregate, \Countable
{
    /**
     * @var DomainEntityInterface $entity
     */
    private $entity;

    /**
     * @var Map $internal_map
     */
    private $internal_map;

    /**
     * @param DomainEntityInterface $entity
     * @param mixed[] $values
     */
    public function __construct(DomainEntityInterface $entity, array $values = [])
    {
        $this->entity = $entity;
        $this->internal_map = new Map;
        foreach ($entity->getEntityType()->getAttributes() as $attr_name => $attribute) {
            $this->internal_map[$attr_name] = $attribute->makeValue($values[$attr_name] ?? null, $this->entity);
        }
    }

    /**
     * @param string $attr_name
     *
     * @return boolean
     */
    public function has(string $attr_name): bool
    {
        return $this->internal_map->hasKey($attr_name);
    }

    /**
     * @param string $attr_name
     *
     * @return ValueObjectInterface
     */
    public function get(string $attr_name): ValueObjectInterface
    {
        return $this->internal_map->get($attr_name);
    }

    /**
     * @param string $attr_name
     * @param mixed $value
     *
     * @return self
     */
    public function withValue(string $attr_name, $value): self
    {
        $cloned_map = clone $this;
        $attribute = $this->entity->getEntityType()->getAttribute($attr_name);
        $cloned_map->internal_map[$attr_name] = $attribute->makeValue($value, $cloned_map->entity);
        return $cloned_map;
    }

    /**
     * @param mixed[] $values
     *
     * @return self
     */
    public function withValues(array $values): self
    {
        $cloned_map = clone $this;
        foreach ($values as $attr_name => $value) {
            $attribute = $cloned_map->entity->getEntityType()->getAttribute($attr_name);
            $cloned_map->internal_map[$attr_name] = $attribute->makeValue($value, $cloned_map->entity);
        }
        return $cloned_map;
    }

    /**
     * @return mixed[]
     */
    public function toNative(): array
    {
        return array_map(function (ValueObjectInterface $value) {
            return $value->toNative();
        }, $this->internal_map->toArray());
    }

    /**
     * @param ValueObjectMap $value_map
     *
     * @return ValueObjectMap
     */
    public function diff(ValueObjectMap $value_map): ValueObjectMap
    {
        $cloned_map = clone $this;
        $cloned_map->internal_map = $this->internal_map->filter(
            function (string $attr_name, ValueObjectInterface $value) use ($value_map): bool {
                return !$value->equals($value_map->get($attr_name));
            }
        );
        return $cloned_map;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->internal_map);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->internal_map->isEmpty();
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        return $this->internal_map->getIterator();
    }

    public function __clone()
    {
        $this->internal_map = new Map($this->internal_map->toArray());
    }
}
