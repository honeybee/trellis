<?php

namespace Trellis\Entity;

use Honeybee\Frames\TypedListTrait;
use Trellis\Error\InvalidType;
use Trellis\ValueObject\ValueObjectInterface;
use Trellis\ValueObject\ValueObjectListInterface;

final class NestedEntityList implements ValueObjectListInterface
{
    use TypedListTrait;

    public static function fromNative($nativeValue): ValueObjectInterface
    {
        // @todo implement
    }

    public static function makeEmpty(): ValueObjectInterface
    {
        return new static;
    }

    /**
     * @param TypedEntityInterface[] $entities
     */
    public function __construct(array $entities = [])
    {
        $this->init($entities, TypedEntityInterface::class);
    }

    /**
     * @param ValueObjectInterface $otherList
     * @return bool
     */
    public function equals(ValueObjectInterface $otherList): bool
    {
        if (!$otherList instanceof ValueObjectListInterface) {
            throw new InvalidType("Trying to composer non-value list to value-object list.");
        }
        if (count($this) !== count($otherList)) {
            return false;
        }
        foreach ($this as $pos => $value) {
            if (!$value->equals($otherList->get($pos))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return mixed[]
     */
    public function toNative(): array
    {
        return $this->compositeVector->map(static function (ValueObjectInterface $entity): array {
            return $entity->toNative();
        })->toArray();
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
        foreach ($this as $pos => $entity) {
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
        foreach ($this as $nestedEntity) {
            $parts[] = (string)$nestedEntity;
        }
        return implode(",\n", $parts);
    }
}
