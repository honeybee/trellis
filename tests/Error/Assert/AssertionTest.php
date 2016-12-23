<?php

namespace Trellis\Tests\Error\Assert;

use Trellis\Entity\ValueObject\GeoPoint;
use Trellis\Error\Assert\Assertion;
use Trellis\Error\AssertionFailed;
use Trellis\Tests\TestCase;

class AssertionTest extends TestCase
{
    public function testCustomExceptionClassIsUsed(): void
    {
        $this->setExpectedException(AssertionFailed::CLASS);
        Assertion::notEmpty(null);
    }

    public function testAssertionsInValueObjectsUseCustomExceptionClass(): void
    {
        $this->setExpectedException(AssertionFailed::CLASS);
        GeoPoint::fromArray([]);
    }
}
