<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectInterface;
use Trellis\Assert\Assertion;

final class Timestamp implements ValueObjectInterface
{
    const NATIVE_FORMAT = 'Y-m-d\TH:i:s.uP';

    const EMPTY = null;

    /**
     * @var string $timestamp
     */
    private $timestamp;

    /**
     * @var string $original_format
     */
    private $original_format;

    /**
     * Create a new Timestamp from the given date-string and format.
     *
     * @param string $date
     * @param string $format
     *
     * @return Timestamp
     */
    public static function createFromString(string $date, string $format = self::NATIVE_FORMAT): Timestamp
    {
        Assertion::date($date, $format);
        return new Timestamp(\DateTimeImmutable::createFromFormat($format, $date), $format);
    }

    /**
     * @param \DateTimeImmutable $timestamp
     * @param string $format
     */
    public function __construct(\DateTimeImmutable $timestamp = self::EMPTY, string $format = self::NATIVE_FORMAT)
    {
        $this->timestamp = $timestamp;
        $this->original_format = $format;
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
        return !$this->isEmpty() ? $this->timestamp->format(self::NATIVE_FORMAT) : self::EMPTY;
    }

    /**
     * @return string
     */
    public function getOriginalFormat(): string
    {
        return $this->original_format;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->isEmpty() ? "null" : $this->toNative();
    }
}
