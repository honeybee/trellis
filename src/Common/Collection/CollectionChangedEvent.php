<?php

namespace Trellis\Common\Collection;

use Trellis\Common\EventInterface;
use Trellis\Common\Object;

class CollectionChangedEvent extends Object implements EventInterface
{
    const ITEM_ADDED = 'added';

    const ITEM_REMOVED = 'removed';

    protected $item;

    protected $type;

    public function __construct($item, $type)
    {
        $this->item = $item;
        $this->type = $type;
    }

    public function getItem()
    {
        return $this->item;
    }

    public function getType()
    {
        return $this->type;
    }
}
