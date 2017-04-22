<?php

namespace Trellis\EntityType;

use Honeybee\Frames\TypedMapTrait;

final class AttributeMap implements \IteratorAggregate, \Countable
{
    use TypedMapTrait;

    /**
     * @param AttributeInterface[] $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->init(array_reduce($attributes, function (array $carry, AttributeInterface $attribute) {
            $carry[$attribute->getName()] = $attribute; // enforce consistent attribute keys
            return $carry;
        }, []), AttributeInterface::class);
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
            $clonedMap->compositeMap = $clonedMap->compositeMap->filter(
                function (string $name, AttributeInterface $attribute) use ($classNames): bool {
                    return in_array(get_class($attribute), $classNames);
                }
            );
        })(...$classNames);
        return $clonedMap;
    }
}
