<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectEqualsTrait;
use Trellis\Entity\ValueObjectInterface;

final class Date implements ValueObjectInterface
{
    use ValueObjectEqualsTrait;

    const FORMAT_ISO8601 = 'Y-m-d';

    private $timestamp;

    /**
     * {@inheritdoc}
     */
    public static function createFromString($date, $format = self::FORMAT_ISO8601)
    {
        return new Date(Timestamp::createFromString($date, $format));
    }

    /**
     * @param Timestamp $timestamp
     */
    public function __construct(Timestamp $timestamp = Timestamp::EMPTY)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->timestamp->isEmpty();
    }

    /**
     * @return null|string
     */
    public function toNative(): ?string
    {
        return $this->timestamp->toNative();
    }
}
