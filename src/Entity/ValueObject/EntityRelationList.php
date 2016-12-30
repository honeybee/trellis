<?php

namespace Trellis\Entity\ValueObject;

use Traversable;
use Trellis\Assert\Assertion;
use Trellis\Entity\ValueObjectInterface;
use Trellis\Entity\ValueObjectList;
use Trellis\EntityRelationInterface;
use Trellis\Entity\ValueObjectListInterface;
use Trellis\Error\MutabilityError;

final class EntityRelationList implements ValueObjectListInterface
{
    /**
     * @var NestedEntityList $internal_list
     */
    private $internal_list;

    /**
     * @param iterable|null|EntityRelationInterface[] $entities
     */
    public function __construct(iterable $entities = null)
    {
        $this->internal_list = new NestedEntityList(
            (function (EntityRelationInterface ...$entities): array {
                return $entities;
            })(...$entities ?? [])
        );
    }

    /**
     * @param ValueObjectListInterface $other_list
     *
     * @return ValueObjectListInterface
     */
    public function diff(ValueObjectListInterface $other_list): ValueObjectListInterface
    {
        return $this->internal_list->diff($other_list);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->internal_list;
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        return $this->internal_list->getIterator();
    }

    /**
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->internal_list->offsetExists($offset);
    }

    /**
     * @param string $offset
     * @return ValueObjectInterface
     */
    public function offsetGet($offset): ValueObjectInterface
    {
        return $this->internal_list->offsetGet($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        throw new MutabilityError("Trying to change immutable EntityRelationList instance.");
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        throw new MutabilityError("Trying to change immutable EntityRelationList instance.");
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->internal_list->count();
    }

    /**
     * @param ValueObjectInterface $other_value
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        /* @var EntityRelationList $other_value */
        Assertion::isInstanceOf($other_value, EntityRelationList::CLASS);
        if (count($this) !== count($other_value)) {
            return false;
        }
        foreach ($this as $pos => $value) {
            if (!$value->equals($other_value->get($pos))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->internal_list->isEmpty();
    }

    /**
     * @return mixed
     */
    public function toNative()
    {
        return $this->internal_list->toNative();
    }

    /**
     * @param ValueObjectInterface $value_object
     *
     * @return null|int
     */
    public function getPos(ValueObjectInterface $value_object): ?int
    {
        return $this->internal_list->getPos($value_object);
    }

    /**
     * @return null|ValueObjectInterface
     */
    public function getFirst(): ?ValueObjectInterface
    {
        return $this->internal_list->getFirst();
    }

    /**
     * @return null|ValueObjectInterface
     */
    public function getLast(): ?ValueObjectInterface
    {
        return $this->internal_list->getLast();
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function has(int $offset): bool
    {
        return $this->internal_list->has($offset);
    }

    /**
     * @param int $offset
     * @return ValueObjectInterface
     */
    public function get(int $offset): ValueObjectInterface
    {
        return $this->internal_list->get($offset);
    }

    /**
     * @param ValueObjectInterface $value_object
     *
     * @return ValueObjectListInterface
     */
    public function add(ValueObjectInterface $value_object): ValueObjectListInterface
    {
        $clone = $this;
        $clone->internal_list = $this->internal_list->add($value_object);
        return $clone;
    }

    /**
     * @param ValueObjectInterface $value_object
     *
     * @return ValueObjectListInterface
     */
    public function remove(ValueObjectInterface $value_object): ValueObjectListInterface
    {
        $clone = $this;
        $clone->internal_list = $this->internal_list->remove($value_object);
        return $clone;
    }
}
