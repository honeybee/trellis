<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Timestamp;
use Trellis\Tests\TestCase;

final class TimestampTest extends TestCase
{
    public function testToNative(): void
    {
        $timestamp = new Timestamp(new \DateTimeImmutable("2016-07-04T19:27:07.000000+02:00")); // with timezone
        $this->assertEquals("2016-07-04T19:27:07.000000+02:00", $timestamp->toNative());
        $this->assertEquals(Timestamp::EMPTY, (new Timestamp)->toNative());
        $timestamp = new Timestamp(new \DateTimeImmutable("2016-07-04T19:27:07")); // without timezone
        $this->assertEquals("2016-07-04T19:27:07.000000+00:00", $timestamp->toNative());
    }

    public function testEquals(): void
    {
        $timestamp = Timestamp::createFromString("2016-07-04T19:27:07", "Y-m-d\\TH:i:s");
        $this->assertTrue(
            $timestamp->equals(Timestamp::createFromString("2016-07-04T19:27:07", "Y-m-d\\TH:i:s"))
        );
        $this->assertFalse(
            $timestamp->equals(Timestamp::createFromString("2017-08-04T19:27:07", "Y-m-d\\TH:i:s"))
        );
    }
}
