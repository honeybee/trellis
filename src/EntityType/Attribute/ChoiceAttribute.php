<?php

namespace Trellis\EntityType\Attribute;

use Ds\Map;
use Trellis\Assert\Assertion;
use Trellis\Entity\ValueObject\Text;
use Trellis\Entity\ValueObjectInterface;
use Trellis\EntityInterface;
use Trellis\EntityType\Attribute;
use Trellis\EntityTypeInterface;
use Trellis\Error\UnexpectedValue;

final class ChoiceAttribute extends Attribute
{
    /**
     * @var TextAttribute $text_attribute
     */
    private $text_attribute;

    /**
     * @var Map $choices
     */
    private $choices;

    /**
     * @param string $name
     * @param EntityTypeInterface $entity_type
     * @param array $params
     */
    public function __construct($name, EntityTypeInterface $entity_type, $params = [])
    {
        parent::__construct($name, $entity_type, $params);
        $this->text_attribute = new TextAttribute($name, $entity_type, $params);
        Assertion::hasArrayParam($this, "options");
        $this->choices = new Map;
        foreach ($this->getParam("options") as $value => $label) {
            $this->choices->put(new Text($value), new Text($label));
        }
    }

    /*
     * @param mixed $value
     * @param EntityInterface $parent The entity that the value is being created for.
     *
     * @return ValueObjectInterface
     */
    public function makeValue($value = null, EntityInterface $parent = null): ValueObjectInterface
    {
        /* @var Text $value */
        $value = $this->text_attribute->makeValue($value, $parent);
        if (!$this->isValidChoice($value)) {
            throw new UnexpectedValue("Trying to create choice from unknown option.");
        }
        return $value;
    }

    /**
     * @param Text $value
     *
     * @return bool
     */
    private function isValidChoice(Text $value): bool
    {
        $valid_choice = false;
        foreach ($this->choices as $choice => $label) {
            if ($choice->equals($value)) {
                $valid_choice = true;
                break;
            }
        }
        return $valid_choice;
    }
}
