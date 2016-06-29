<?php

namespace Trellis\Collection;

use Trellis\Exception;

class TypedList extends ItemList
{
    use ItemTypeConstraint;
}
