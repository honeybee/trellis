<?php

namespace Trellis\Runtime\Validator\Rule;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueValueInterface;
use Trellis\Runtime\Validator\Rule\RuleInterface;

class RuleList extends TypedList implements UniqueValueInterface
{
    protected function getItemImplementor()
    {
        return RuleInterface::CLASS;
    }
}
