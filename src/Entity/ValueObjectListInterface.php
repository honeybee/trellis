<?php

namespace Trellis\Entity;

interface ValueObjectListInterface extends ValueObjectInterface, \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @param ValueObjectInterface $value_object
     *
     * @return ValueObjectList
     */
    public function add(ValueObjectInterface $value_object): ValueObjectListInterface;

    /**
     * @param ValueObjectInterface $value_object
     *
     * @return ValueObjectList
     */
    public function remove(ValueObjectInterface $value_object): ValueObjectListInterface;

    /**
     * @param ValueObjectInterface $value_object
     *
     * @return null|int
     */
    public function getPos(ValueObjectInterface $value_object): ?int;

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
     * @param ValueObjectListInterface $other_list
     * @return ValueObjectListInterface
     */
    public function diff(ValueObjectListInterface $other_list): ValueObjectListInterface;
}
