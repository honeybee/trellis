<?php

namespace Trellis\EntityType\Attribute\Choice;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Choice implements ValueInterface
{
    use NativeEqualsComparison;

    const NIL = '';

    /**
     * @var string $choice
     */
    private $choice;

    /**
     * @var string[] $allowed_choices
     */
    private $allowed_choices;

    /**
     * @param string[] $allowed_choices
     * @param string $choice
     */
    public function __construct(array $allowed_choices, $choice = self::NIL)
    {
        Assertion::allString($allowed_choices);
        Assertion::string($choice);
        if ($choice !== self::NIL) {
            Assertion::inArray($choice, $allowed_choices);
        }

        $this->choice = $choice;
        $this->allowed_choices = $allowed_choices;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->choice === self::NIL;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return $this->choice;
    }

    /**
     * @return string[]
     */
    public function getAllowedChoices()
    {
        return $this->allowed_choices;
    }
}
