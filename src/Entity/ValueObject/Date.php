<?php

namespace Trellis\Entity\ValueObject;

use Assert\Assertion;
use Trellis\Entity\ValueObjectInterface;

final class Date implements ValueObjectInterface
{
    const NATIVE_FORMAT = 'Y-m-d';

    const EMPTY = null;

    /**
     * @var string $date
     */
    private $date;

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
     * @return Date
     */
    public static function createFromString(string $date, string $format = self::NATIVE_FORMAT): Date
    {
        Assertion::date($date, $format);
        return new Date((\DateTimeImmutable::createFromFormat($format, $date)), $format);
    }

    /**
     * @param \DateTimeImmutable $date
     * @param string $original_format
     */
    public function __construct(\DateTimeImmutable $date = self::EMPTY, string $original_format = self::NATIVE_FORMAT)
    {
        $this->date = $date ? $date->setTime(0, 0, 0) : $date;
        $this->original_format = $original_format;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        Assertion::isInstanceOf($other_value, Date::CLASS);
        return $this->toNative() === $other_value->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->date === self::EMPTY;
    }

    /**
     * @return null|string
     */
    public function toNative(): ?string
    {
        return !$this->isEmpty() ? $this->date->format(self::NATIVE_FORMAT) : self::EMPTY;
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
        return !$this->isEmpty() ? $this->date->format($this->original_format) : "";
    }
}
