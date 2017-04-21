<?php

namespace Trellis\EntityType;

use Ds\Map;

final class AttributeMap implements \IteratorAggregate, \Countable
{
    /**
     * @var Map $internalMap
     */
    private $internalMap;

    /**
     * @param iterable|null|AttributeInterface[] $attributes
     */
    public function __construct(iterable $attributes = null)
    {
        $this->internalMap = new Map;
        (function (AttributeInterface ...$attributes): void {
            foreach ($attributes as $attribute) {
                $this->internalMap->put($attribute->getName(), $attribute);
            }
        })(...$attributes ?? []);
    }

    /**
     * @param string $attributeName
     * @return boolean
     */
    public function has(string $attributeName): bool
    {
        return $this->internalMap->hasKey($attributeName);
    }

    /**
     * @param string $attributeName
     * @return AttributeInterface
     */
    public function get(string $attributeName): AttributeInterface
    {
        return $this->internalMap->get($attributeName);
    }

    /**
     * Returns the type"s attribute collection filter by a set of attribute classes-names.
     * @param string[] $classNames A list of attribute-classes to filter for.
     * @return self
     */
    public function byClassNames(array $classNames = []): self
    {
        $clonedMap = clone $this;
        (function (string ...$classNames) use ($clonedMap): void {
            $clonedMap->internalMap = $clonedMap->internalMap->filter(
                function (string $name, AttributeInterface $attribute) use ($classNames): bool {
                    return in_array(get_class($attribute), $classNames);
                }
            );
        })(...$classNames);
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
