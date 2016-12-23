<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectEqualsTrait;
use Trellis\Entity\ValueObjectInterface;

final class Boolean implements ValueObjectInterface
{
    use ValueObjectEqualsTrait;

    const EMPTY = false;

    /**
     * @var bool $boolean
     */
    private $boolean;

    /**
     * @param bool $boolean
     */
    public function __construct(bool $boolean = self::EMPTY)
    {
        $this->boolean = $boolean;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->boolean === self::EMPTY;
    }

    /**
     * @return bool
     */
    public function toNative(): bool
    {
        return $this->boolean;
    }
}
