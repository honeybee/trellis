<?php

namespace Trellis\Entity\Path;

use Trellis\Collection\TypedList;

class ValuePath extends TypedList
{
    public function __construct($path_parts = [])
    {
        parent::__construct(ValuePathPart::CLASS, $path_parts);
    }

    public function __toString()
    {
        return array_reduce($this->items, function ($path, ValuePathPart $path_part) {
            if (empty($path)) {
                return (string)$path_part;
            }
            return $path.'-'.$path_part;
        }, '');
    }
}
