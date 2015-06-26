<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueCollectionInterface;

class SpecificationMap extends TypedMap implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return SpecificationInterface::CLASS;
    }
}
