<?php

namespace Trellis\Runtime\ValueHolder;

use Trellis\Common\Collection\CollectionChangedEvent;
use Trellis\Common\Collection\CollectionInterface;
use Trellis\Common\Collection\ListenerInterface;
use Trellis\Common\EventInterface;
use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Entity\EntityChangedEvent;
use Trellis\Runtime\Entity\EntityChangedListenerInterface;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Entity\EntityList;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Result\ResultInterface;

/**
 * Basic ValueHolderInterface implementation that all other Values should inherit from.
 */
abstract class ValueHolder implements ValueHolderInterface, ListenerInterface, EntityChangedListenerInterface
{
    /**
     * Holds attribute which's data we are handling.
     *
     * @var AttributeInterface $attribute
     */
    private $attribute;

    /**
     * Holds the valueholder's current value.
     *
     * @var mixed $value
     */
    private $value;

    /**
     * Holds a list of listeners registered to our value changed event.
     *
     * @var ValueChangedListenerList $listeners
     */
    private $listeners;

    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param mixed $other_value value to compare to the internal one
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    abstract protected function valueEquals($other_value);

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    abstract public function toNative();

    /**
     * Returns the type of the value that is returned for the toNative() call.
     * This is used for typehints in code generation and might be used in other
     * layers (e.g. web form submissions) to prune empty values from array
     * request parameters (when this method returns 'array'), e.g. "foo[bar][]"
     * as checkboxes in a form will contain empty values for unchecked
     * checkboxes. To know the native type is helpful to handle such a case
     * as the validation rule can't distinguish between deliberate and wrongly
     * given empty strings.
     *
     * @return string return type of the toNative() method
     */
    abstract public function getNativeType();

    /**
     * Returns the type of the internal value of the valueholder. This can
     * be anything from 'string', 'array' or 'int' to a fully qualified class
     * name of the value object or PHP object used for storage internally.
     *
     * The returned type is the one returned by getValue() method calls.
     *
     * @return string type or FQCN of the internal value
     */
    abstract public function getValueType();

    /**
     * Contructs a new valueholder instance, that is dedicated to the given attribute.
     *
     * @param AttributeInterface $attribute
     */
    public function __construct(AttributeInterface $attribute)
    {
        $this->attribute = $attribute;
        $this->listeners = new ValueChangedListenerList();
        $this->value = $attribute->getNullValue();
    }

    /**
     * Returns the valueholder's value.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the valueholder's value.
     *
     * @param mixed $value
     * @param EntityInterface $entity
     *
     * @return ResultInterface
     */
    public function setValue($value, EntityInterface $entity = null)
    {
        $attribute_validator = $this->getAttribute()->getValidator();
        $validation_result = $attribute_validator->validate($value, $entity);

        if ($validation_result->getSeverity() <= IncidentInterface::NOTICE) {
            $previous_native_value = $this->toNative();
            $this->value = $validation_result->getSanitizedValue();

            if (!$this->sameValueAs($previous_native_value)) {
                $value_changed_event = $this->createValueHolderChangedEvent($previous_native_value);
                $this->propagateValueChangedEvent($value_changed_event);
            }

            if ($this->value instanceof CollectionInterface) {
                $this->value->addListener($this);
            }
            if ($this->value instanceof EntityList) {
                $this->value->addEntityChangedListener($this);
            }
        }

        return $validation_result;
    }

    /**
     * Tells whether the valueholder's value is considered to be the same
     * as the empty/null defined on the attribute.
     *
     * @return boolean
     */
    public function isNull()
    {
        return $this->sameValueAs($this->attribute->getNullValue());
    }

    /**
     * Tells whether the valueholder's value is considered to be the same as
     * the default value defined on the attribute.
     *
     * @return boolean
     */
    public function isDefault()
    {
        return $this->sameValueAs($this->attribute->getDefaultValue());
    }

    /**
     * Tells whether a specific ValueHolderInterface instance's value is
     * considered equal to the given value.
     *
     * @param mixed $other_value
     *
     * @return boolean
     */
    public function sameValueAs($other_value)
    {
        $null_value = $this->attribute->getNullValue();
        if ($null_value === $this->getValue() && $null_value === $other_value) {
            return true;
        }

        $validation_result = $this->getAttribute()->getValidator()->validate($other_value);
        if ($validation_result->getSeverity() !== IncidentInterface::SUCCESS) {
            return false;
        }

        return $this->valueEquals($validation_result->getSanitizedValue());
    }

    /**
     * Tells whether a valueholder is considered being equal to the given
     * valueholder. That is, class and value are the same. The attribute and
     * entity may be different.
     *
     * @param ValueHolderInterface $other_value_holder
     *
     * @return boolean
     */
    public function isEqualTo(ValueHolderInterface $other_value_holder)
    {
        if (get_class($this) !== get_class($other_value_holder)) {
            return false;
        }

        return $this->sameValueAs($other_value_holder->getValue());
    }

    /**
     * Registers a given listener as a recipient of value changed events.
     *
     * @param ValueChangedListenerInterface $listener
     */
    public function addValueChangedListener(ValueChangedListenerInterface $listener)
    {
        if (!$this->listeners->hasItem($listener)) {
            $this->listeners->push($listener);
        }
    }

    /**
     * Removes a given listener as from our list of value-changed listeners.
     *
     * @param ValueChangedListenerInterface $listener
     */
    public function removedValueChangedListener(ValueChangedListenerInterface $listener)
    {
        if ($this->listeners->hasItem($listener)) {
            $this->listeners->removeItem($listener);
        }
    }

    /**
     * Callback function that is invoked when an underlying collection value changes.
     *
     * @param CollectionChangedEvent $event
     */
    public function onCollectionChanged(CollectionChangedEvent $event)
    {
        // @todo need to find out what to use as the prev value here
        $this->propagateValueChangedEvent(
            $this->createValueHolderChangedEvent($this->toNative())
        );
    }

    /**
     * Handles entity changed events that are sent by our embedd entity.
     *
     * @param EntityChangedEvent $embedded_entity_event
     */
    public function onEntityChanged(EntityChangedEvent $embedded_entity_event)
    {
        $value_changed_event = $embedded_entity_event->getValueChangedEvent();

        $this->propagateValueChangedEvent(
            new ValueChangedEvent(
                array(
                    'attribute_name' => $value_changed_event->getAttributeName(),
                    'prev_value' => $value_changed_event->getOldValue(),
                    'value' => $value_changed_event->getNewValue(),
                    'embedded_event' => $embedded_entity_event
                )
            )
        );
    }

    /**
     * Returns the attribute that we are handling the data for.
     *
     * @return AttributeInterface
     */
    protected function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Propagates a given value changed event to all corresponding listeners.
     *
     * @param ValueChangedEvent $event
     */
    protected function propagateValueChangedEvent(ValueChangedEvent $event)
    {
        foreach ($this->listeners as $listener) {
            $listener->onValueChanged($event);
        }
    }

    /**
     * Create a new value-changed event instance from the given info.
     *
     * @param mixed $prev_value
     * @param EventInterface $embedded_event
     */
    protected function createValueHolderChangedEvent($prev_value, EventInterface $embedded_event = null)
    {
        return new ValueChangedEvent(
            array(
                'attribute_name' => $this->getAttribute()->getName(),
                'prev_value' => $prev_value,
                'value' => $this->toNative(),
                'embedded_event' => $embedded_event
            )
        );
    }
}
