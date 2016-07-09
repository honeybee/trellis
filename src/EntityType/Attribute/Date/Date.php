<?php

namespace Trellis\EntityType\Attribute\Date;

use Assert\Assertion;
use DateTimeImmutable;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Date implements ValueInterface
{
    use NativeEqualsComparison;

    const NIL = null;

    const FORMAT_ISO8601 = 'Y-m-d';

    /**
     * @var DateTimeImmutable $date
     */
    private $date;

    /**
     * @var string $format
     */
    private $format;

    /**
     * Create a new Date from the given date-string and format.
     *
     * @param string $date_format
     * @param string $format
     *
     * @return Date
     */
    public static function createFromString($date_string, $format = self::FORMAT_ISO8601)
    {
        Assertion::string($format);
        Assertion::date($date_string, $format);

        return new static(DateTimeImmutable::createFromFormat($format, $date_string), $format);
    }

    /**
     * @param DateTimeImmutable $date
     */
    public function __construct(DateTimeImmutable $date = self::NIL, $format = self::FORMAT_ISO8601)
    {
        Assertion::string($format);

        $this->format = $format;
        $this->date = $date ? $date->setTime(0, 0, 0) : self::NIL;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->date === self::NIL;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return !$this->isEmpty() ? $this->date->format($this->format) : null;
    }
}
