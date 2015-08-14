<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentMap;
use Trellis\Runtime\Validator\Rule\Rule;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;

class TimestampRule extends Rule
{
    const DEFAULT_FORCE_INTERNAL_TIMEZONE = true;
    const DEFAULT_INTERNAL_TIMEZONE_NAME = 'UTC';

    const OPTION_FORCE_INTERNAL_TIMEZONE = 'force_internal_timezone';
    const OPTION_INTERNAL_TIMEZONE_NAME = 'internal_timezone_name';
    const OPTION_MAX_TIMESTAMP = 'max_timestamp';
    const OPTION_MIN_TIMESTAMP = 'min_timestamp';
    const OPTION_FORMAT_NATIVE = 'format_native';

    const FORMAT_ISO8601 = 'Y-m-d\TH:i:s.uP';
    const FORMAT_ISO8601_SIMPLE = 'Y-m-d\TH:i:sP';
    const FORMAT_ISO8601_DATE = 'Y-m-dP';
    const FORMAT_ISO8601_DATE_SIMPLE = 'Y-m-d';

    const FORMAT_NATIVE = 'Y-m-d\TH:i:s.uP';

    /**
     * @overridden
     */
    public function apply($value, EntityInterface $entity = null)
    {
        $this->incidents = new IncidentMap();
        $this->sanitized_value = null;

        if (true === ($success = $this->execute($value, $entity))) {
            if ($this->sanitized_value === null && $value !== '') {
                $this->sanitized_value = $value;
            }
        }

        return $success;
    }

    protected function execute($value, EntityInterface $entity = null)
    {
        $default_timezone = new DateTimeZone(
            $this->getOption(
                self::OPTION_INTERNAL_TIMEZONE_NAME,
                self::DEFAULT_INTERNAL_TIMEZONE_NAME
            )
        );

        $null_value = $this->getOption(AttributeInterface::OPTION_NULL_VALUE, null);
        if ($value === $null_value || $value === '') {
            // accept empty values as valid when no mandatory handling happens in this rule
            $this->setSanitizedValue($null_value);
            return true;
        }

        if (is_string($value)) {
            if ($value === 'now') {
                $dt = DateTimeImmutable::createFromFormat(
                    'U.u',
                    sprintf('%.6F', microtime(true))
                );
            } elseif ($value === '') {
                // this is the toNative return value for the nullValue
                $dt = false;
            } else {
                $dt = new DateTimeImmutable($value);
            }

            if ($dt === false) {
                $this->throwError('invalid_string', [ 'value' => $value ]);
                return false;
            }
        } elseif ($value instanceof DateTime) {
            if (version_compare(PHP_VERSION, '5.6.0') >= 0) {
                $dt = DateTimeImmutable::createFromMutable($value);
            } else {
                $dt = DateTimeImmutable::createFromFormat(
                    self::FORMAT_ISO8601,
                    $value->format(self::FORMAT_ISO8601)
                );
            }
        } elseif ($value instanceof DateTimeImmutable) {
            $dt = clone $value;
        } else {
            $this->throwError('invalid_type', [ 'value' => $value ]);
            return false;
        }

        $force_internal_timezone = $this->getOption(
            self::OPTION_FORCE_INTERNAL_TIMEZONE,
            self::DEFAULT_FORCE_INTERNAL_TIMEZONE
        );
        if ($force_internal_timezone) {
            $dt = $dt->setTimezone($default_timezone);
        }

        if ($this->hasOption(self::OPTION_MIN_TIMESTAMP)) {
            $min = new DateTimeImmutable($this->getOption(self::OPTION_MIN_TIMESTAMP));
            $force_internal_timezone = $this->getOption(
                self::OPTION_FORCE_INTERNAL_TIMEZONE,
                self::DEFAULT_FORCE_INTERNAL_TIMEZONE
            );
            if ($force_internal_timezone) {
                $min->setTimezone($default_timezone);
            }

            // compare via PHP internal and then compare microseconds as well m(
            if (!( ($dt >= $min) && ((int)$dt->format('u') >= (int)$min->format('u')) )) {
                $this->throwError('min', [ 'value' => $dt, 'min_value' => $min ]);
                return false;
            }
        }

        if ($this->hasOption(self::OPTION_MAX_TIMESTAMP)) {
            $max = new DateTimeImmutable($this->getOption(self::OPTION_MAX_TIMESTAMP));
            $force_internal_timezone = $this->getOption(
                self::OPTION_FORCE_INTERNAL_TIMEZONE,
                self::DEFAULT_FORCE_INTERNAL_TIMEZONE
            );
            if ($force_internal_timezone) {
                $max->setTimezone($default_timezone);
            }

            if (!( ($dt <= $max) && ((int)$dt->format('u') <= (int)$max->format('u')) )) {
                $this->throwError('max', [ 'value' => $dt, 'max_value' => $max ]);
                return false;
            }
        }

        $this->setSanitizedValue($dt);

        return true;
    }
}
