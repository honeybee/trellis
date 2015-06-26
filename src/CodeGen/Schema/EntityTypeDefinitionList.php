<?php

namespace Trellis\CodeGen\Schema;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueCollectionInterface;

class EntityTypeDefinitionList extends TypedList implements UniqueCollectionInterface
{
    protected function getItemImplementor()
    {
        return '\\Trellis\\CodeGen\\Schema\\EntityTypeDefinition';
    }
}
