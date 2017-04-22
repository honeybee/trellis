<?php

namespace Trellis\Entity;

use Honeybee\Frames\TypedListTrait;
use Trellis\Assert\Assertion;
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
        Assertion::isInstanceOf($otherList, static::class);
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
     * @return mixed[]
     */
    public function toNative(): array
    {
        return $this->compositeVector->map(static function (ValueObjectInterface $entity): array {
            return $entity->toNative();
        })->toArray();
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
