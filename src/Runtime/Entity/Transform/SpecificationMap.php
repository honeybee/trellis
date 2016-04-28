<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueValueInterface;

class SpecificationMap extends TypedMap implements UniqueValueInterface
{
    protected function getItemImplementor()
    {
        return SpecificationInterface::CLASS;
    }
}
