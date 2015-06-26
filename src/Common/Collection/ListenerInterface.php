<?php

namespace Trellis\Common\Collection;

interface ListenerInterface
{
    public function onCollectionChanged(CollectionChangedEvent $event);
}
