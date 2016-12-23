<?php

namespace Trellis\EntityType;

use Ds\Map;

final class AttributeMap implements \IteratorAggregate, \Countable
{
    /**
     * @var Map $internal_map
     */
    private $internal_map;

    /**
     * @param iterable|null|AttributeInterface[] $attributes
     */
    public function __construct(iterable $attributes = null)
    {
        $this->internal_map = new Map;
        (function (AttributeInterface ...$attributes): void {
            foreach ($attributes as $attribute) {
                $this->internal_map->put($attribute->getName(), $attribute);
            }
        })(...$attributes ?? []);
    }

    /**
     * @param string $attribute_name
     *
     * @return boolean
     */
    public function has(string $attribute_name): bool
    {
        return $this->internal_map->hasKey($attribute_name);
    }

    /**
     * @param string $attribute_name
     *
     * @return AttributeInterface
     */
    public function get(string $attribute_name): AttributeInterface
    {
        return $this->internal_map->get($attribute_name);
    }

    /**
     * Returns the type's attribute collection filter by a set of attribute classes-names.
     *
     * @param string[] $class_names A list of attribute-classes to filter for.
     *
     * @return self
     */
    public function byClassNames(array $class_names = []): self
    {
        $cloned_map = clone $this;
        (function (string ...$class_names) use ($cloned_map): void {
            $cloned_map->internal_map = $cloned_map->internal_map->filter(
                function (AttributeInterface $attribute) use ($class_names): bool {
                    return in_array(get_class($attribute), $class_names);
                }
            );
        })(...$class_names);
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
