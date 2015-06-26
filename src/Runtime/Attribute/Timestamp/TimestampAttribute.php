<?php

namespace Trellis\Runtime\Attribute\Timestamp;

use Trellis\Runtime\Attribute\Attribute;
use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\EntityTypeInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\RuleList;
use Trellis\Runtime\Validator\Rule\Type\TimestampRule;

// preferred exchange format is FORMAT_ISO8601 ('Y-m-d\TH:i:s.uP')
class TimestampAttribute extends Attribute
{
    const DEFAULT_FORCE_INTERNAL_TIMEZONE   = TimestampRule::DEFAULT_FORCE_INTERNAL_TIMEZONE;
    const DEFAULT_INTERNAL_TIMEZONE_NAME    = TimestampRule::DEFAULT_INTERNAL_TIMEZONE_NAME;

    const OPTION_FORCE_INTERNAL_TIMEZONE    = TimestampRule::OPTION_FORCE_INTERNAL_TIMEZONE;
    const OPTION_INTERNAL_TIMEZONE_NAME     = TimestampRule::OPTION_INTERNAL_TIMEZONE_NAME;
    const OPTION_MAX_TIMESTAMP              = TimestampRule::OPTION_MAX_TIMESTAMP;
    const OPTION_MIN_TIMESTAMP              = TimestampRule::OPTION_MIN_TIMESTAMP;
    const OPTION_FORMAT_NATIVE              = TimestampRule::OPTION_FORMAT_NATIVE;

    const FORMAT_ISO8601                    = TimestampRule::FORMAT_ISO8601;
    const FORMAT_ISO8601_SIMPLE             = TimestampRule::FORMAT_ISO8601_SIMPLE;
    const FORMAT_ISO8601_DATE               = TimestampRule::FORMAT_ISO8601_DATE;
    const FORMAT_ISO8601_DATE_SIMPLE        = TimestampRule::FORMAT_ISO8601_DATE_SIMPLE;

    const FORMAT_NATIVE                     = self::FORMAT_ISO8601;

    /**
     * Constructs a new attribute instance with some default options.
     *
     * @param string $name
     * @param EntityTypeInterface $type,
     * @param array $options
     * @param AttributeInterface $parent
     */
    public function __construct(
        $name,
        EntityTypeInterface $type,
        array $options = [],
        AttributeInterface $parent = null
    ) {
        if (!array_key_exists(self::OPTION_FORCE_INTERNAL_TIMEZONE, $options)) {
            $options[self::OPTION_FORCE_INTERNAL_TIMEZONE] = self::DEFAULT_FORCE_INTERNAL_TIMEZONE;
        }

        if (!array_key_exists(self::OPTION_INTERNAL_TIMEZONE_NAME, $options)) {
            $options[self::OPTION_INTERNAL_TIMEZONE_NAME] = self::DEFAULT_INTERNAL_TIMEZONE_NAME;
        }

        parent::__construct($name, $type, $options, $parent);
    }

    public function getNullValue()
    {
        return $this->getOption(self::OPTION_NULL_VALUE, null);
    }

    public function getDefaultValue()
    {
        $default_value = $this->getOption(self::OPTION_DEFAULT_VALUE, $this->getNullValue());
        if (empty($default_value) || $default_value === 'null') {
            return $this->getNullValue();
        }

        $validation_result = $this->getValidator()->validate($default_value);
        if ($validation_result->getSeverity() > IncidentInterface::NOTICE) {
            throw new InvalidConfigException(
                sprintf(
                    "Configured default_value for attribute '%s' on entity type '%s' is not valid.",
                    $this->getName(),
                    $this->getType() ? $this->getType()->getName() : 'undefined'
                )
            );
        }

        return $validation_result->getSanitizedValue();
    }

    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = [];

        if ($this->hasOption(self::OPTION_MIN_TIMESTAMP)) {
            $options[self::OPTION_MIN_TIMESTAMP] = $this->getOption(self::OPTION_MIN_TIMESTAMP);
        }

        if ($this->hasOption(self::OPTION_MAX_TIMESTAMP)) {
            $options[self::OPTION_MAX_TIMESTAMP] = $this->getOption(self::OPTION_MAX_TIMESTAMP);
        }

        $options[self::OPTION_FORCE_INTERNAL_TIMEZONE] = $this->getOption(
            self::OPTION_FORCE_INTERNAL_TIMEZONE,
            self::DEFAULT_FORCE_INTERNAL_TIMEZONE
        );
        $options[self::OPTION_INTERNAL_TIMEZONE_NAME] = $this->getOption(
            self::OPTION_INTERNAL_TIMEZONE_NAME,
            self::DEFAULT_INTERNAL_TIMEZONE_NAME
        );

        $valid_datetime_rule = new TimestampRule('valid-timestamp', $options);

        $rules->push($valid_datetime_rule);

        return $rules;
    }
}
