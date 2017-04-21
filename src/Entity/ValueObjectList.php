<?php

namespace Trellis\Entity;

use Ds\Vector;
use Trellis\Error\InvalidType;
use Trellis\Error\MutabilityError;
use Trellis\ValueObject\ValueObjectInterface;

abstract class ValueObjectList implements ValueObjectListInterface
{
    /**
     * @var Vector $internalVector
     */
    protected $internalVector;

    /**
     * @param iterable|ValueObjectInterface[] $values
     */
    public function __construct(iterable $values = null)
    {
        $this->internalVector = new Vector;
        (function (ValueObjectInterface ...$values): void {
            foreach ($values as $value) {
                $this->internalVector->push($value);
            }
        })(...$values ?? []);
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
        if (count($this->internalVector) !== count($otherList)) {
            return false;
        }
        foreach ($this->internalVector as $pos => $value) {
            if (!$value->equals($otherList->get($pos))) {
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
        return $this->internalVector->isEmpty();
    }

    /**
     * @return mixed[]
     */
    public function toNative(): array
    {
        return $this->internalVector->map(static function (ValueObjectInterface $entity): array {
            return $entity->toNative();
        })->toArray();
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function has(int $offset): bool
    {
        return isset($this->internalVector[$offset]);
    }

    /**
     * @param int $offset
     * @return ValueObjectInterface
     */
    public function get(int $offset): ValueObjectInterface
    {
        return $this->internalVector[$offset];
    }

    /**
     * @param ValueObjectInterface $valueObject
     * @return null|int
     */
    public function getPos(ValueObjectInterface $valueObject): ?int
    {
        $pos = $this->internalVector->find($valueObject);
        return $pos === false ? null : $pos;
    }

    /**
     * @return ValueObjectInterface
     */
    public function getFirst(): ValueObjectInterface
    {
        return $this->internalVector->first();
    }

    /**
     * @return ValueObjectInterface
     */
    public function getLast(): ValueObjectInterface
    {
        return $this->internalVector->last();
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->internalVector);
    }

    /**
     * @param ValueObjectInterface $valueObject
     * @return ValueObjectListInterface
     */
    public function add(ValueObjectInterface $valueObject): ValueObjectListInterface
    {
        $clonedList = clone $this;
        $clonedList->internalVector->push($valueObject);
        return $clonedList;
    }

    /**
     * @param ValueObjectInterface $valueObject
     * @return ValueObjectListInterface
     */
    public function remove(ValueObjectInterface $valueObject): ValueObjectListInterface
    {
        $clonedList = clone $this;
        $clonedList->internalVector->remove($this->internalVector->find($valueObject));
        return $clonedList;
    }

    /**
     * @param int $offset
     * @return ValueObjectInterface
     */
    public function &offsetGet($offset): ValueObjectInterface
    {
        return $this->internalVector[$offset];
    }

    /**
     * @param int $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->internalVector[$offset]);
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        return $this->internalVector->getIterator();
    }

    /**
     * @param int $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new MutabilityError("Trying to change immutable EntityList instance.");
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset): void
    {
        throw new MutabilityError("Trying to change immutable EntityList instance.");
    }

    public function __clone()
    {
        $this->internalVector = new Vector($this->internalVector->toArray());
    }
}
