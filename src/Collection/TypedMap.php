<?php

namespace Trellis\Collection;

use Trellis\Common\Error\InvalidTypeException;

class TypedMap extends Map
{
    use HasValueTypeConstraint;
}
