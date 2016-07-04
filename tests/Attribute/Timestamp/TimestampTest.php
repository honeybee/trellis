<?php

namespace Trellis\Tests\Attribute\Timestamp;

use DateTimeImmutable;
use Trellis\Attribute\Timestamp\Timestamp;
use Trellis\Tests\TestCase;
use Trellis\Value\ValueInterface;

class TimestampTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new Timestamp(new DateTimeImmutable));
    }

    public function testToNative()
    {
        $native_val = '2016-07-04T19:27:07.000000+02:00';
        $timestamp = Timestamp::createFromString($native_val);
        $this->assertEquals($native_val, $timestamp->toNative());

        $timestamp = new Timestamp;
        $this->assertNull($timestamp->toNative());
    }

    public function testIsEmpty()
    {
        $native_val = '2016-07-04T19:27:07.000000+02:00';
        $timestamp = Timestamp::createFromString($native_val);
        $this->assertFalse($timestamp->isEmpty());

        $timestamp = new Timestamp(null);
        $this->assertTrue($timestamp->isEmpty());

        $timestamp = new Timestamp;
        $this->assertTrue($timestamp->isEmpty());
    }
}
