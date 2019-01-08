<?php

namespace Trellis\Runtime\Entity;

use Closure;
use Trellis\Common\Collection\CollectionChangedEvent;
use Trellis\Common\Collection\TypedList;

/**
 * EntityList is a TypedList implementation, that holds Ientities and provides some extra convenience.
 * You can attach to it as an EntityChangedListenerInterface and will be notified
 * on all events occuring from it's contained entities.
 */
class EntityList extends TypedList implements EntityChangedListenerInterface
{
    /**
     * Holds all currently attached entity-changed listeners.
     *
     * @var EntityChangedListenerList $listeners
     */
    protected $listeners;

    /**
     * Create/construct a new entity list instance.
     */
    public function __construct(array $entities = [])
    {
        parent::__construct($entities);

        $this->listeners = new EntityChangedListenerList();
    }

    /**
     * Attaches a given entity-changed listener,
     * which will be notified about any changes on contained entities.
     *
     * @param EntityChangedListenerInterface $listener
     */
    public function addEntityChangedListener(EntityChangedListenerInterface $listener)
    {
        if (!$this->listeners->hasItem($listener)) {
            $this->listeners->push($listener);
        }
    }

    /**
     * Detaches the given entity-changed listener.
     *
     * @param EntityChangedListenerInterface $listener
     */
    public function removeEntityChangedListener(EntityChangedListenerInterface $listener)
    {
        if ($this->listeners->hasItem($listener)) {
            $this->listeners->removeItem($listener);
        }
    }

    /**
     * Handles entity-changed events that are sent by our embedd entities.
     *
     * @param EntityChangedEvent $event
     */
    public function onEntityChanged(EntityChangedEvent $event)
    {
        $this->propagateEntityChangedEvent($event);
    }

    /**
     * Returns an array representation of the current entity list.
     *
     * @return array
     */
    public function toArray()
    {
        return array_map(
            function (EntityInterface $entity) {
                return $entity->toArray();
            },
            $this->items
        );
    }

    /**
     * Returns whether the entities in the list have the same entity type
     *
     * @return boolean
     */
    public function containsMultipleTypes()
    {
        $mixed = false;

        $types = [];
        foreach ($this->items as $item) {
            $class = get_class($item->getType());
            if (!in_array($class, $types, true)) {
                $types[] = $class;
            }
        }

        if (count($types) > 1) {
            $mixed = true;
        }

        return $mixed;
    }

    /**
     * Returns the entity in the list with the specified identifier.
     *
     * @return EntityInterface
     */
    public function getEntityByIdentifier($identifier)
    {
        $found_entities = $this->filter(
            function (EntityInterface $entity) use ($identifier) {
                return $entity->getIdentifier() === $identifier;
            }
        );

        return $found_entities->getFirst();
    }

    /**
     * Propagates a given entity-changed event to all attached entity-changed listeners.
     *
     * @param EntityChangedEvent $event
     */
    protected function propagateEntityChangedEvent(EntityChangedEvent $event)
    {
        foreach ($this->listeners as $listener) {
            $listener->onEntityChanged($event);
        }
    }

    /**
     * Propagates a given collection-changed event to all attached collection-changed listeners.
     *
     * @param CollectionChangedEvent $event
     */
    protected function propagateCollectionChangedEvent(CollectionChangedEvent $event)
    {
        if ($event->getType() === CollectionChangedEvent::ITEM_REMOVED) {
            $event->getItem()->removeEntityChangedListener($this);
        } else {
            $event->getItem()->addEntityChangedListener($this);
        }

        parent::propagateCollectionChangedEvent($event);
    }

    /**
     * Returns the EntityInterface interface-name to the TypeList parent-class,
     * which uses this info to implement it's type/instanceof strategy.
     *
     * @return string
     */
    protected function getItemImplementor()
    {
        return EntityInterface::CLASS;
    }

    protected function cloneItem($item)
    {
        return clone $item;
    }
}
