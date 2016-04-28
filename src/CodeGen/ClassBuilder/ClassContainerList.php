<?php

namespace Trellis\CodeGen\ClassBuilder;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueValueInterface;

class ClassContainerList extends TypedList implements UniqueValueInterface
{
    protected function getItemImplementor()
    {
        return '\\Trellis\\CodeGen\\ClassBuilder\\ClassContainer';
    }
}
