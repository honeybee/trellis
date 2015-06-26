<?php

namespace Trellis\CodeGen\ClassBuilder;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueCollectionInterface;

class ClassContainerList extends TypedList implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return '\\Trellis\\CodeGen\\ClassBuilder\\ClassContainer';
    }
}
