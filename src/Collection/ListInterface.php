<?php

namespace Trellis\Collection;

interface ListInterface extends CollectionInterface
{
    /**
     * @param mixed $value
     *
     * @return ListInterface
     */
    public function push($value);

    /**
     * @return ListInterface
     */
    public function pop();

    /**
     * Moves the given value to the given position, if the value is already contained by the list.
     *
     * @param $pos
     * @param $value
     *
     * @return ListInterface
     */
    public function moveTo($pos, $value);

    /**
     * Inserts the given value into the list at the given position.
     *
     * @param $pos
     * @param $value
     *
     * @return ListInterface
     */
    public function insertAt($pos, $value);

    /**
     * @param int $pos
     * @param int $length
     * @param array $values
     *
     * @return ListInterface
     */
    public function splice($pos, $length = 1, array $values = []);

    /**
     * @return ListInterface
     */
    public function shift();

    /**
     * @param mixed $value
     *
     * @return ListInterface
     */
    public function unshift($value);

    /**
     * @return mixed
     */
    public function getFirst();

    /**
     * @return mixed
     */
    public function getLast();

    /**
     * @param CollectionInterface $collection
     *
     * @return ListInterface
     */
    public function append(CollectionInterface $collection);

    /**
     * @return ListInterface
     */
    public function reverse();
}
