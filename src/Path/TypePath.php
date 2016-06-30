<?php

namespace Trellis\Path;

use Trellis\Collection\TypedList;

class TypePath extends TypedList implements PathInterface
{
    public function __construct($path_parts)
    {
        parent::__construct(TypePathPart::CLASS, $path_parts);
    }

    public function __toString()
    {
        return array_reduce($this->items, function ($path, PathPartInterface $path_part) {
            if (empty($path)) {
                return $path_part;
            }
            return $path.'-'.$path_part;
        }, '');
    }
}
