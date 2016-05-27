<?php

namespace Trellis\Runtime\Entity;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueKeyInterface;
use Trellis\Common\Collection\UniqueValueInterface;

class EntityMap extends TypedMap implements UniqueKeyInterface, UniqueValueInterface
{
    // @todo implement immutable map with constructor for setting identifier keys

    protected function getItemImplementor()
    {
        return EntityInterface::CLASS;
    }
}
