<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectEqualsTrait;
use Trellis\Entity\ValueObjectInterface;

final class Integer implements ValueObjectInterface
{
    use ValueObjectEqualsTrait;

    const EMPTY = null;

    /**
     * @var int $integer
     */
    private $integer;

    /**
     * @param int $integer
     */
    public function __construct(int $integer = self::EMPTY)
    {
        $this->integer = $integer;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->integer === self::EMPTY;
    }

    /**
     * @return null|int
     */
    public function toNative(): ?int
    {
        return $this->integer;
    }
}
