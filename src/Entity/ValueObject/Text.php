<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectInterface;
use Trellis\Error\Assert\Assertion;

final class Text implements ValueObjectInterface
{
    const EMPTY = '';

    /**
     * @var string $text
     */
    private $text;

    /**
     * @param string $text
     */
    public function __construct(string $text = self::EMPTY)
    {
        $this->text = $text;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        Assertion::isInstanceOf($other_value, Text::CLASS);
        return $this->toNative() === $other_value->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->text === self::EMPTY;
    }

    /**
     * @return string
     */
    public function toNative(): string
    {
        return $this->text;
    }
}
