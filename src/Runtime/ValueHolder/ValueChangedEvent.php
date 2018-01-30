<?php

namespace Trellis\Runtime\ValueHolder;

use Trellis\Common\BaseObject;
use Trellis\Common\EventInterface;
use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Entity\EntityChangedEvent;

/**
 * ValueChangedEvent(s) reflect state changes to an entity's values.
 *
 * These events are fired everytime an entity attribute's value
 * actually changes and can be used to track state changes over time.
 */
class ValueChangedEvent extends BaseObject implements EventInterface
{
    /**
     * Holds the event's attribute origin.
     *
     * @var string
     */
    protected $attribute_name;

    /**
     * Holds the previous value (via toNative) of the attribute origin.
     *
     * @var mixed $prev_value
     */
    protected $prev_value;

    /**
     * Holds the new value (via toNative) of the attribute origin.
     *
     * @var mixed $value
     */
    protected $value;

    /**
     * Holds the time at which the event was created.
     *
     * @var int $timestamp
     */
    protected $timestamp;

    /**
     * Holds a possibly embedded entity's value changed event.
     *
     * @var EntityChangedEvent $embedded_event
     */
    protected $embedded_event;

    /**
     * Constructs a new ValueChangedEvent instance.
     */
    public function __construct(array $state = [])
    {
        $this->timestamp = time();

        parent::__construct($state);
    }

    /**
     * Returns the event's affected attribute.
     *
     * @return string
     */
    public function getAttributeName()
    {
        return $this->attribute_name;
    }

    /**
     * Returns the previous value of the event's related attribute.
     *
     * @return mixed native representation of the attribute's old value
     */
    public function getOldValue()
    {
        return $this->prev_value;
    }

    /**
     * Returns the new value of the event's related attribute.
     *
     * @return mixed native representation of the attribute's new value
     */
    public function getNewValue()
    {
        return $this->value;
    }

    /**
     * Returns the event's creation time as a unix timestamp.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * If the originating attribute is an embed attribute,
     * this method returns an embedd entity's underlying value changed event.
     *
     * @return ValueChangedEvent
     */
    public function getEmbeddedEvent()
    {
        return $this->embedded_event;
    }

    /**
     * Returns a string representation of the current event.
     *
     * @return string
     */
    public function __toString()
    {
        $old = $this->getOldValue();
        $new = $this->getNewValue();

        $string_representation = sprintf(
            "The `%s` attribute's value changed from '%s' to '%s'",
            $this->getAttributeName(),
            var_export($this->getOldValue(), true),
            var_export($this->getNewValue(), true)
        );

        if (($embedded_event = $this->getEmbeddedEvent())) {
            $string_representation .= PHP_EOL . "The actual changed occured upon the attribute's embed though.";
            $string_representation .= PHP_EOL . $embedded_event;
        }

        return $string_representation;
    }
}
