<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectInterface;
use Trellis\Error\Assert\Assertion;

final class Timestamp implements ValueObjectInterface
{
    const FORMAT_ISO8601 = 'Y-m-d\TH:i:s.uP';

    const EMPTY = null;

    /**
     * @var string $timestamp
     */
    private $timestamp;

    /**
     * @var string $format
     */
    private $format;

    /**
     * Create a new Timestamp from the given date-string and format.
     *
     * @param string $date
     * @param string $format
     *
     * @return Timestamp
     */
    public static function createFromString(string $date, string $format = self::FORMAT_ISO8601): Timestamp
    {
        Assertion::date($date, $format);
        return new Timestamp(\DateTimeImmutable::createFromFormat($format, $date), $format);
    }

    /**
     * @param \DateTimeImmutable $timestamp
     * @param string $format
     */
    public function __construct(\DateTimeImmutable $timestamp = self::EMPTY, string $format = self::FORMAT_ISO8601)
    {
        $this->timestamp = $timestamp;
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        Assertion::isInstanceOf($other_value, Timestamp::CLASS);
        return $this->toNative() === $other_value->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->timestamp === self::EMPTY;
    }

    /**
     * @return null|string
     */
    public function toNative(): ?string
    {
        return !$this->isEmpty() ? $this->timestamp->format($this->format) : self::EMPTY;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }
}
