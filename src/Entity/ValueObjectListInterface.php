<?php

namespace Trellis\Entity;

use Trellis\ValueObject\ValueObjectInterface;

interface ValueObjectListInterface extends ValueObjectInterface, \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @param ValueObjectInterface $valueObject
     * @return ValueObjectList
     */
    public function add(ValueObjectInterface $valueObject): ValueObjectListInterface;

    /**
     * @param ValueObjectInterface $valueObject
     * @return ValueObjectList
     */
    public function remove(ValueObjectInterface $valueObject): ValueObjectListInterface;

    /**
     * @param ValueObjectInterface $valueObject
     * @return null|int
     */
    public function getPos(ValueObjectInterface $valueObject): ?int;

    /**
     * @return null|ValueObjectInterface
     */
    public function getFirst(): ?ValueObjectInterface;

    /**
     * @return null|ValueObjectInterface
     */
    public function getLast(): ?ValueObjectInterface;

    /**
     * @param int $offset
     * @return bool
     */
    public function has(int $offset): bool;

    /**
     * @param int $offset
     * @return ValueObjectInterface
     */
    public function get(int $offset): ValueObjectInterface;

    /**
     * @param ValueObjectListInterface $otherList
     * @return ValueObjectListInterface
     */
    public function diff(ValueObjectListInterface $otherList): ValueObjectListInterface;
}
