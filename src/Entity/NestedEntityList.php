<?php

namespace Trellis\Entity;

use Trellis\ValueObject\ValueObjectInterface;

final class NestedEntityList extends ValueObjectList
{
    public static function fromNative($nativeValue, array $context = [])
    {

    }

    public static function makeEmpty(): ValueObjectInterface
    {
        return new static;
    }

    /**
     * @param iterable|null|TypedEntityInterface[] $entities
     */
    public function __construct(iterable $entities = null)
    {
        parent::__construct(
            (function (TypedEntityInterface ...$entities): array {
                return $entities;
            })(...$entities ?? [])
        );
    }

    /**
     * @param ValueObjectListInterface $otherList
     *
     * @return ValueObjectListInterface
     */
    public function diff(ValueObjectListInterface $otherList): ValueObjectListInterface
    {
        $differentEntities = [];
        /* @var TypedEntityInterface $entity */
        foreach ($this->internalVector as $pos => $entity) {
            if (!$otherList->has($pos)) {
                $differentEntities[] = $entity;
                continue;
            }
            /* @var TypedEntityInterface $otherEntity */
            $otherEntity = $otherList->get($pos);
            $diff = $entity->getValueObjectMap()->diff($otherEntity->getValueObjectMap());
            if (!$diff->isEmpty()) {
                $differentEntities[] = $entity;
            }
        }
        return new static($differentEntities);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $parts = [];
        foreach ($this->internalVector as $nestedEntity) {
            $parts[] = (string)$nestedEntity;
        }
        return implode(",\n", $parts);
    }
}
