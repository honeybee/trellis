<?php

namespace Trellis\Collection;

class TypedList extends ItemList
{
    use ItemTypeConstraint;
    
    public function __construct($item_type, array $items = [])
    {
        $this->item_type = $item_type;

        parent::__construct($items);
    }
}
