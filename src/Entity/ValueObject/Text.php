<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectEqualsTrait;
use Trellis\Entity\ValueObjectInterface;

final class Text implements ValueObjectInterface
{
    use ValueObjectEqualsTrait;

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
