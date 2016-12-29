<?php

namespace Trellis\Assert;

use Assert\Assertion as BaseAssertion;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityTypeInterface;
use Trellis\Error\AssertionFailed;

final class Assertion extends BaseAssertion
{
    const MISSING_PARAM = 1000;

    protected static $exceptionClass = AssertionFailed::CLASS;

    /**
     * @param EntityTypeInterface|AttributeInterface $param_container
     * @param string $param_name
     * @param string|null $message
     * @param string|null $property_path
     *
     * @return bool
     */
    public static function hasParam(
        $param_container,
        string $param_name,
        string $message = null,
        string $property_path = null
    ): bool
    {
        if (!$param_container->hasParam($param_name)) {
            throw static::createException($param_name, $message, static::MISSING_PARAM, $property_path);
        }
        return true;
    }

    public static function hasArrayParam(
        $param_holder,
        string $param_name,
        string $message = null,
        string $property_path = null
    ): bool
    {
        self::hasParam($param_holder, $param_name, $message, $property_path);
        self::isArray($param_holder->getParam($param_name), $message);
        return true;
    }

    /**
     * Make a string version of a value.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected static function stringify($value)
    {
        return parent::stringify($value);
    }
}
