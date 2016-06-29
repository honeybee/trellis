<?php

namespace Trellis\Collection;

interface ListInterface extends CollectionInterface
{
    public function push($value);

    public function pop();

    public function shift();

    public function unshift($value);

    public function reverse();

    public function getFirst();

    public function getLast();
}
