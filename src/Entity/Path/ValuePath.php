<?php

namespace Trellis\Entity\Path;

use Ds\Vector;
use Trellis\Assert\Assertion;
use Trellis\Entity\NestedEntity;
use Trellis\Entity\TypedEntityInterface;

final class ValuePath implements \IteratorAggregate, \Countable
{
    /**
     * @var Vector
     */
    private $internalVector;

    /**
     * @param TypedEntityInterface $entity
     * @return ValuePath
     */
    public static function fromEntity(TypedEntityInterface $entity): self
    {
        $parentEntity = $entity->getEntityParent();
        $currentEntity = $entity;
        $valuePath = new ValuePath;
        while ($parentEntity) {
            /* @var NestedEntity $currentEntity */
            Assertion::isInstanceOf($currentEntity, NestedEntity::class);
            $attributeName = $currentEntity->getEntityType()->getParentAttribute()->getName();
            /* @var NestedEntityList $entityList */
            $entityList = $parentEntity->get($attributeName);
            $entityPos = $entityList->getPos($currentEntity);
            $valuePath = $valuePath->push(new ValuePathPart($attributeName, $entityPos));
            $currentEntity = $parentEntity;
            $parentEntity = $parentEntity->getEntityParent();
        }
        return $valuePath->reverse();
    }

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
