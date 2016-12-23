<?php

namespace Trellis\EntityType\Path;

use Ds\Vector;

final class TypePath implements \IteratorAggregate, \Countable
{
   /**
     * @var Vector $internal_vector
     */
    private $internal_vector;

    /**
     * @param iterable|TypePathPart[] $path_parts
     */
    public function __construct(iterable $path_parts = null)
    {
        $this->internal_vector = new Vector(
            (function (TypePathPart ...$path_parts): array {
                return $path_parts;
            })(...$path_parts ?? [])
        );
    }

    /**
     * @param TypePathPart $path_part
     *
     * @return TypePath
     */
    public function push(TypePathPart $path_part): TypePath
    {
        $cloned_path = clone $this;
        $cloned_path->internal_vector->push($path_part);
        return $cloned_path;
    }

    /**
     * @return TypePath
     */
    public function reverse(): TypePath
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
        $flattenPath = function (string $path, TypePathPart $path_part): string {
            return empty($path) ? (string)$path_part : "$path-$path_part";
        };
        return $this->internal_vector->reduce($flattenPath, '');
    }

    public function __clone()
    {
        $this->internal_vector = new Vector($this->internal_vector->toArray());
    }
}
