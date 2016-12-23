<?php

namespace Trellis\EntityType;

use Ds\Map;
use Trellis\EntityTypeInterface;

final class EntityTypeMap implements \IteratorAggregate, \Countable
{
    /**
     * @var Map $internal_map
     */
    private $internal_map;

    /**
     * @param iterable|null|EntityTypeInterface[] $types
     */
    public function __construct(iterable $types = null)
    {
        $this->internal_map = new Map;
        (function (EntityTypeInterface ...$types): void {
            foreach ($types as $type) {
                $this->internal_map->put($type->getPrefix(), $type);
            }
        })(...$types ?? []);
    }

    /**
     * @param string $type_prefix
     *
     * @return boolean
     */
    public function has(string $type_prefix): bool
    {
        return $this->internal_map->hasKey($type_prefix);
    }

    /**
     * @param string $type_prefix
     *
     * @return null|EntityTypeInterface
     */
    public function get(string $type_prefix): ?EntityTypeInterface
    {
        return $this->internal_map->get($type_prefix);
    }

    /**
     * @param string $name
     *
     * @return null|EntityTypeInterface
     */
    public function byName(string $name): ?EntityTypeInterface
    {
        foreach ($this->internal_map as $type) {
            if ($type->getName() === $name) {
                return $type;
            }
        }
        return null;
    }

    /**
     * @param string $class_name
     *
     * @return null|EntityTypeInterface
     */
    public function byClassName(string $class_name): ?EntityTypeInterface
    {
        foreach ($this->internal_map as $type) {
            if (get_class($type) === $class_name) {
                return $type;
            }
        }
        return null;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->internal_map);
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
