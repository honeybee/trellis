<?php

namespace Trellis\Assert;

use Assert\Assertion as BaseAssertion;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityTypeInterface;
use Trellis\Error\AssertionFailed;

final class Assertion extends BaseAssertion
{
    const MISSING_PARAM = 1000;

    protected static $exceptionClass = AssertionFailed::class;

    /**
     * @param EntityTypeInterface|AttributeInterface $paramContainer
     * @param string $paramName
     * @param string|null $message
     * @param string|null $propertyPath
     *
     * @return bool
     */
    public static function hasParam(
        $paramContainer,
        string $paramName,
        string $message = null,
        string $propertyPath = null
    ): bool {
        if (!$paramContainer->hasParam($paramName)) {
            throw static::createException($paramName, $message, static::MISSING_PARAM, $propertyPath);
        }
        return true;
    }

    /**
     * @param $paramContainer
     * @param string $paramName
     * @param string|null $message
     * @param string|null $propertyPath
     *
     * @return bool
     */
    public static function hasArrayParam(
        $paramContainer,
        string $paramName,
        string $message = null,
        string $propertyPath = null
    ): bool {
        self::hasParam($paramContainer, $paramName, $message, $propertyPath);
        self::isArray($paramContainer->getParam($paramName), $message);
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
