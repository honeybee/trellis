<?php

namespace Trellis\Runtime\Entity;

/**
 * Represents a listener to events that occur, when a entity instance changes one of it's values.
 */
interface EntityChangedListenerInterface
{
    /**
     * Handle entity changed events.
     *
     * @param EntityChangedEvent $event
     */
    public function onEntityChanged(EntityChangedEvent $event);
}
