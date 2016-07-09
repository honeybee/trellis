<?php

namespace Trellis\EntityType\Attribute\ReferenceList;

use Assert\Assertion;
use Trellis\EntityType\Attribute\EntityList\EntityList;
use Trellis\Entity\ReferenceInterface;

class ReferenceList extends EntityList
{
    protected function guardConstraints(array $items)
    {
        parent::guardConstraints($items);

        Assertion::allIsInstanceOf(ReferenceInterface::CLASS, $items);
    }
}
