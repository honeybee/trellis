<?php

namespace Trellis\Entity\Path;

use Ds\Vector;

final class ValuePath implements \IteratorAggregate, \Countable
{
    /**
     * @var Vector $internal_vector
     */
    private $internal_vector;

    /**
     * @param iterable|ValuePathPart[]|null $path_parts
     */
    public function __construct(iterable $path_parts = null)
    {
        $this->internal_vector = new Vector(
            (function (ValuePathPart ...$path_parts): array {
                return $path_parts;
            })(...$path_parts ?? [])
        );
    }

    /**
     * @param ValuePathPart $path_part
     *
     * @return ValuePath
     */
    public function push(ValuePathPart $path_part): ValuePath
    {
        $cloned_path = clone $this;
        $cloned_path->internal_vector->push($path_part);
        return $cloned_path;
    }

    /**
     * @return ValuePath
     */
    public function reverse(): ValuePath
    {
        $cloned_path = clone $this;
        $cloned_path->internal_vector->reverse();
        return $cloned_path;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->internal_vector);
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        return $this->internal_vector->getIterator();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $flattenPath = function (string $path, ValuePathPart $path_part): string {
            return empty($path) ? (string)$path_part : "$path-$path_part";
        };
        return $this->internal_vector->reduce($flattenPath, "");
    }

    public function __clone()
    {
        $this->internal_vector = new Vector($this->internal_vector->toArray());
    }
}
