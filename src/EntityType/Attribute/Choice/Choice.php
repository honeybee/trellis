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
     * @param float $choice
     */
    public function __construct(array $allowed_choices, $choice = self::NIL)
    {
        Assertion::string($choice);
        if ($choice !== self::NIL) {
            Assertion::inArray($choice, $allowed_choices);
        }

        $this->choice = $choice;
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
}
