<?php

namespace Trellis\EntityType\Path;

use Trellis\Collection\TypedList;

class TypePath extends TypedList
{
    public function __construct($path_parts)
    {
        parent::__construct(TypePathPart::CLASS, $path_parts);
    }

    public function __toString()
    {
        return array_reduce($this->items, function ($path, TypePathPart $path_part) {
            if (empty($path)) {
                return (string)$path_part;
            }
            return $path.'-'.$path_part;
        }, '');
    }
}
