<?php

namespace Trellis\Attribute\Timestamp;

use Assert\Assertion;
use DateTimeImmutable;
use Trellis\Value\NativeEqualsComparison;
use Trellis\Value\ValueInterface;

class Timestamp implements ValueInterface
{
    const FORMAT_ISO8601 = 'Y-m-d\TH:i:s.uP';

    use NativeEqualsComparison;

    /**
     * @var string $timestamp
     */
    private $timestamp;

    /**
     * Create a new Timestamp from the given date-string and format.
     *
     * @param string $date_format
     * @param string $format
     *
     * @return Timestamp
     */
    public static function createFromString($date_string, $format = self::FORMAT_ISO8601)
    {
        Assertion::date($date_string, $format);

        return new static(DateTimeImmutable::createFromFormat($format, $date_string));
    }

    /**
     * @param string $timestamp
     */
    public function __construct(DateTimeImmutable $timestamp = null)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->timestamp === null;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return !$this->isEmpty() ? $this->timestamp->format(self::FORMAT_ISO8601) : null;
    }
}
