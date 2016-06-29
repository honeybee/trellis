<?php

namespace Trellis\Collection;

interface ListInterface extends CollectionInterface
{
    /**
     * @param mixed $item
     *
     * @return ListInterface
     */
    public function push($item);

    /**
     * @return ListInterface
     */
    public function pop();

    /**
     * Moves the given item to the given position, if the item is already contained by the list.
     *
     * @param $pos
     * @param $item
     *
     * @return ListInterface
     */
    public function moveTo($pos, $item);

    /**
     * Inserts the given item into the list at the given position.
     *
     * @param $pos
     * @param $item
     *
     * @return ListInterface
     */
    public function insertAt($pos, $item);

    /**
     * @param int $pos
     * @param int $length
     * @param array $items
     *
     * @return ListInterface
     */
    public function splice($pos, $length = 1, array $items = []);

    /**
     * @return ListInterface
     */
    public function shift();

    /**
     * @param mixed $item
     *
     * @return ListInterface
     */
    public function unshift($item);

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
