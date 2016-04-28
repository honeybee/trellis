<?php

namespace Trellis\CodeGen\Schema;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueValueInterface;

class EntityTypeDefinitionList extends TypedList implements UniqueValueInterface
{
    protected function getItemImplementor()
    {
        return '\\Trellis\\CodeGen\\Schema\\EntityTypeDefinition';
    }
}
