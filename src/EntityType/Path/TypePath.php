<?php

namespace Trellis\EntityType\Path;

use Ds\Vector;
use Trellis\EntityType\AttributeInterface;

final class TypePath implements \IteratorAggregate, \Countable
{
   /**
     * @var Vector
     */
    private $internalVector;

    /**
     * Returns attribute path of this attribute. Depending on this attribute
     * being part of a nested-entity this may look like this format:
     * {attribute_name}.{type_prefix}.{attribute_name}
     * @param AttributeInterface $attribute
     * @return TypePath
     */
    public static function fromAttribute(AttributeInterface $attribute): self
    {
        $currentAttribute = $attribute->getParent();
        $currentType = $attribute->getEntityType();
        $pathLeaf = new TypePathPart($attribute->getName());
        $typePath = new TypePath([ $pathLeaf ]);
        while ($currentAttribute) {
            $pathPart = new TypePathPart($currentAttribute->getName(), $currentType->getPrefix());
            $typePath = $typePath->push($pathPart);
            $currentAttribute = $currentAttribute->getParent();
            if ($currentAttribute) {
                $currentType = $currentAttribute->getEntityType();
            }
        }
        return $typePath->reverse();
    }

    /**
     * @param iterable|TypePathPart[] $pathParts
     */
    public function __construct(iterable $pathParts = null)
    {
        $this->internalVector = new Vector(
            (function (TypePathPart ...$pathParts): array {
                return $pathParts;
            })(...$pathParts ?? [])
        );
    }

    /**
     * @param TypePathPart $pathPart
     * @return TypePath
     */
    public function push(TypePathPart $pathPart): TypePath
    {
        $clonedPath = clone $this;
        $clonedPath->internalVector->push($pathPart);
        return $clonedPath;
    }

    /**
     * @return TypePath
     */
    public function reverse(): TypePath
    {
        $clonedPath = clone $this;
        $clonedPath->internalVector->reverse();
        return $clonedPath;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->internalVector);
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        return $this->internalVector->getIterator();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $flattenPath = function (string $path, TypePathPart $pathPart): string {
            return empty($path) ? (string)$pathPart : "$path-$pathPart";
        };
        return $this->internalVector->reduce($flattenPath, "");
    }

    public function __clone()
    {
        $this->internalVector = new Vector($this->internalVector->toArray());
    }
}
