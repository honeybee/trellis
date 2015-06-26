<?php

namespace Trellis\Runtime\Validator\Rule;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueCollectionInterface;
use Trellis\Runtime\Validator\Rule\RuleInterface;

class RuleList extends TypedList implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return RuleInterface::CLASS;
    }
}
