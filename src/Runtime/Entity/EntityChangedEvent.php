<?php

namespace Trellis\Runtime\Entity;

use Trellis\Common\EventInterface;
use Trellis\Runtime\ValueHolder\ValueChangedEvent;

/**
 * Represents an event that occurs when a entity's value changes.
 * Entity changes are triggered on a per attribute base.
 */
class EntityChangedEvent implements EventInterface
{
    /**
     * Holds a reference to the entity instance that changed.
     *
     * @var EntityInterface $entity
     */
    private $entity;

    /**
     * Holds the value changed event that reflects our change origin.
     *
     * @var ValueChangedEvent $value_changed_event
     */
    private $value_changed_event;

    /**
     * Constructs a new EntityChangedEvent instance.
     *
     * @param EntityInterface $entity
     * @param ValueChangedEvent $value_changed_event
     */
    public function __construct(EntityInterface $entity, ValueChangedEvent $value_changed_event)
    {
        $this->entity = $entity;
        $this->value_changed_event = $value_changed_event;
    }

    /**
     * Returns the affected entity.
     *
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Returns the value changed origin event.
     *
     * @return ValueChangedEvent
     */
    public function getValueChangedEvent()
    {
        return $this->value_changed_event;
    }

     /**
     * Returns a string representation of the current event.
     *
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            "[%s] A %s type's entity attribute value has changed: \n %s",
            get_class($this),
            $this->getEntity()->getType()->getName(),
            $this->getValueChangedEvent()
        );
    }
}
