<?php

namespace Trellis\EntityType\Attribute\Choice;

use Trellis\EntityType\Attribute\Attribute;
use Trellis\EntityType\Attribute\Choice\Choice;
use Trellis\Entity\EntityInterface;

class ChoiceAttribute extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function createValue(EntityInterface $parent, $value = null)
    {
        if ($value instanceof Choice) {
            $value = $value->toNative();
        }
        $allowed_choices = $this->getOption('allowed_values', []);

        return $value !== null ? new Choice($allowed_choices, $value) : new Choice($allowed_choices);
    }
}
