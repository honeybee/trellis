<?php

namespace Trellis\CodeGen\Schema;

use Trellis\Common\Collection\TypedList;
use Trellis\Common\Collection\UniqueValueInterface;

class AttributeDefinitionList extends TypedList implements UniqueValueInterface
{
    public function filterByType($type)
    {
        return $this->filter(
            function ($attribute) use ($type) {
                return $attribute->getShortName() === $type;
            }
        );
    }

    protected function getItemImplementor()
    {
        return '\\Trellis\\CodeGen\\Schema\\AttributeDefinition';
    }
}
