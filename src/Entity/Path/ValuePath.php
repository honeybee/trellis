<?php

namespace Trellis\Entity\Path;

use Ds\Vector;

final class ValuePath implements \IteratorAggregate, \Countable
{
    /**
     * @var Vector
     */
    private $internalVector;

    /**
     * @param iterable|ValuePathPart[]|null $pathParts
     */
    public function __construct(iterable $pathParts = null)
    {
        $this->internalVector = new Vector(
            (function (ValuePathPart ...$pathParts): array {
                return $pathParts;
            })(...$pathParts ?? [])
        );
    }

    /**
     * @param ValuePathPart $pathPart
     *
     * @return ValuePath
     */
    public function push(ValuePathPart $pathPart): ValuePath
    {
        $clonedPath = clone $this;
        $clonedPath->internalVector->push($pathPart);
        return $clonedPath;
    }

    /**
     * @return ValuePath
     */
    public function reverse(): ValuePath
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
        $flattenPath = function (string $path, ValuePathPart $pathPart): string {
            return empty($path) ? (string)$pathPart : "$path-$pathPart";
        };
        return $this->internalVector->reduce($flattenPath, "");
    }

    public function __clone()
    {
        $this->internalVector = new Vector($this->internalVector->toArray());
    }
}
