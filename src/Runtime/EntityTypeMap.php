<?php

namespace Trellis\Runtime;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueCollectionInterface;
use Trellis\Runtime\EntityTypeInterface;

class EntityTypeMap extends TypedMap implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return EntityTypeInterface::CLASS;
    }
}
