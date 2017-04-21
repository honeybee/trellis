<?php

namespace Trellis\EntityType;

use Ds\Map;

final class EntityTypeMap implements \IteratorAggregate, \Countable
{
    /**
     * @var Map
     */
    private $internalMap;

    /**
     * @param iterable|null|EntityTypeInterface[] $types
     */
    public function __construct(iterable $types = null)
    {
        $this->internalMap = new Map;
        (function (EntityTypeInterface ...$types): void {
            foreach ($types as $type) {
                $this->internalMap->put($type->getPrefix(), $type);
            }
        })(...$types ?? []);
    }

    /**
     * @param string $typePrefix
     * @return boolean
     */
    public function has(string $typePrefix): bool
    {
        return $this->internalMap->hasKey($typePrefix);
    }

    /**
     * @param string $typePrefix
     * @return null|EntityTypeInterface
     */
    public function get(string $typePrefix): ?EntityTypeInterface
    {
        return $this->internalMap->get($typePrefix);
    }

    /**
     * @param string $name
     * @return null|EntityTypeInterface
     */
    public function byName(string $name): ?EntityTypeInterface
    {
        foreach ($this->internalMap as $type) {
            if ($type->getName() === $name) {
                return $type;
            }
        }
        return null;
    }

    /**
     * @param string $className
     * @return null|EntityTypeInterface
     */
    public function byClassName(string $className): ?EntityTypeInterface
    {
        foreach ($this->internalMap as $type) {
            if (get_class($type) === $className) {
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
        return count($this->internalMap);
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        return $this->internalMap->getIterator();
    }
}
