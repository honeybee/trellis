<?php

namespace Trellis\Tests\Assert;

use Trellis\Entity\ValueObject\GeoPoint;
use Trellis\Assert\Assertion;
use Trellis\Tests\TestCase;

final class AssertionTest extends TestCase
{
    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testCustomExceptionClassIsUsed(): void
    {
        Assertion::notEmpty(null);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testAssertionsInValueObjectsUseCustomExceptionClass(): void
    {
        GeoPoint::fromArray([]);
    } // @codeCoverageIgnore
}
