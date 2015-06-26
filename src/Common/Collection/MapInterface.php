<?php

namespace Trellis\Common\Collection;

interface MapInterface extends CollectionInterface
{
    public function setItem($key, $item);

    public function setItems(array $items);

    public function getKeys();

    public function getValues();
}
