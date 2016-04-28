<?php

namespace Trellis\Runtime\Validator\Result;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueValueInterface;

class IncidentMap extends TypedMap implements UniqueValueInterface
{
    protected function getItemImplementor()
    {
        return IncidentInterface::CLASS;
    }
}
