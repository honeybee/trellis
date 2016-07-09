<?php

namespace Trellis\EntityType\Attribute\Date;

use DateTimeImmutable;
use Trellis\EntityType\Attribute\Timestamp\Timestamp;

class Date extends Timestamp
{
    use NativeEqualsComparison;

    const FORMAT_ISO8601 = 'Y-m-d';

    /**
     * {@inheritdoc}
     */
    public static function createFromString($date_string, $format = self::FORMAT_ISO8601)
    {
        return parent::createFromString($date_string, $format);
    }

    /**
     * @param DateTimeImmutable $date
     */
    public function __construct(DateTimeImmutable $date = self::NIL, $format = self::FORMAT_ISO8601)
    {
        parent::__construct($date ? $date->setTime(0, 0, 0) : self::NIL, $format);
    }
}
