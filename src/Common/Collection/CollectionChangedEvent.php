<?php

namespace Trellis\Common\Collection;

use Trellis\Common\BaseObject;
use Trellis\Common\EventInterface;

class CollectionChangedEvent extends BaseObject implements EventInterface
{
    const ITEM_ADDED = 'added';

    const ITEM_REMOVED = 'removed';

    protected $item;

    protected $type;

    public function __construct($item, $type)
    {
        parent::__construct([
            'item' => $item,
            'type' => $type,
        ]);
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
