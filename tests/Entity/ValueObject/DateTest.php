<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Date;
use Trellis\Entity\ValueObject\Timestamp;
use Trellis\Tests\TestCase;

final class DateTest extends TestCase
{
    public function testToNative(): void
    {
        $timestamp = new Date(new \DateTimeImmutable("2016-07-04"));
        $this->assertEquals("2016-07-04", $timestamp->toNative());
        $this->assertEquals(Timestamp::EMPTY, (new Date)->toNative());
    }

    public function testEquals(): void
    {
        $date = Date::createFromString("2016-07-04");
        $this->assertTrue($date->equals(Date::createFromString("2016-07-04")));
        $this->assertTrue(
            $date->equals(Date::createFromString("2016-07-04T19:27:07", "Y-m-d\\TH:i:s"))
        );
        $this->assertFalse($date->equals(Date::createFromString("2017-08-10")));
    }
}
