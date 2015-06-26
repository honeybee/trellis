<?php

namespace Trellis\Common\Collection;

interface ListInterface extends CollectionInterface
{
    public function addItem($item);

    public function addItems(array $items);

    public function push($item);

    public function pop();

    public function shift();

    public function unshift($item);

    public function getFirst();

    public function getLast();

    public function reverse();
}
