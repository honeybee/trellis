<?php

namespace Trellis\Runtime\Validator\Result;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueCollectionInterface;

class IncidentMap extends TypedMap implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return IncidentInterface::CLASS;
    }
}
