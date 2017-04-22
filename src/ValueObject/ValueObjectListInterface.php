<?php

namespace Trellis\ValueObject;

interface ValueObjectListInterface extends ValueObjectInterface, \Countable, \IteratorAggregate
{
    /**
     * @param ValueObjectListInterface $otherList
     * @return ValueObjectListInterface
     */
    public function diff(ValueObjectListInterface $otherList): self;
}
