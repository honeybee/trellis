<?php

namespace Trellis\Entity;

use Ds\Vector;
use Trellis\Error\InvalidType;
use Trellis\Error\MutabilityError;

abstract class ValueObjectList implements ValueObjectListInterface
{
    /**
     * @var Vector $internal_vector
     */
    protected $internal_vector;

    /**
     * @param iterable|ValueObjectInterface[] $values
     */
    public function __construct(iterable $values = null)
    {
        $this->internal_vector = new Vector;
        (function (ValueObjectInterface ...$values): void {
            foreach ($values as $value) {
                $this->internal_vector->push($value);
            }
        })(...$values ?? []);
    }

    /**
     * @param ValueObjectInterface $other_list
     *
     * @return bool
     */
    public function equals(ValueObjectInterface $other_list): bool
    {
        if (!$other_list instanceof ValueObjectListInterface) {
            throw new InvalidType("Trying to composer non-value list to value-object list.");
        }
        if (count($this->internal_vector) !== count($other_list)) {
            return false;
        }
        foreach ($this->internal_vector as $pos => $value) {
            if (!$value->equals($other_list->get($pos))) {
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
        return $this->internal_vector->isEmpty();
    }

    /**
     * @return mixed[]
     */
    public function toNative(): array
    {
        return $this->internal_vector->map(static function (ValueObjectInterface $entity): array {
            return $entity->toNative();
        })->toArray();
    }

    /**
     * @param int $offset
     *
     * @return bool
     */
    public function has(int $offset): bool
    {
        return isset($this->internal_vector[$offset]);
    }

    /**
     * @param int $offset
     *
     * @return ValueObjectInterface
     */
    public function get(int $offset): ValueObjectInterface
    {
        return $this->internal_vector[$offset];
    }

    /**
     * @param ValueObjectInterface $value_object
     *
     * @return null|int
     */
    public function getPos(ValueObjectInterface $value_object): ?int
    {
        $pos = $this->internal_vector->find($value_object);
        return $pos === false ? null : $pos;
    }

    /**
     * @return ValueObjectInterface
     */
    public function getFirst(): ValueObjectInterface
    {
        return $this->internal_vector->first();
    }

    /**
     * @return ValueObjectInterface
     */
    public function getLast(): ValueObjectInterface
    {
        return $this->internal_vector->last();
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->internal_vector);
    }

    /**
     * @param int $offset
     *
     * @return ValueObjectInterface
     */
    public function &offsetGet($offset): ValueObjectInterface
    {
        return $this->internal_vector[$offset];
    }

    /**
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->internal_vector[$offset]);
    }

    /**
     * @return \Iterator
     */
    public function getIterator(): \Iterator
    {
        return $this->internal_vector->getIterator();
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
        $this->internal_vector = new Vector($this->internal_vector->toArray());
    }
}
