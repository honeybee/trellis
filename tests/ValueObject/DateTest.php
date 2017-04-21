<?php

namespace Trellis\Tests\ValueObject;

use Trellis\Tests\TestCase;
use Trellis\ValueObject\Date;

final class DateTest extends TestCase
{
    private const DATE = "2016-07-04";

    /**
     * @var Date
     */
    private $date;

    public function testToNative(): void
    {
        $this->assertEquals(self::DATE, $this->date->toNative());
        $this->assertNull(Date::makeEmpty()->toNative());
    }

    public function testEquals(): void
    {
        $sameDate = Date::fromNative(self::DATE);
        $this->assertTrue($this->date->equals($sameDate));
        $sameDateOtherFormat = Date::createFromString("2016-07-04T19:27:07", "Y-m-d\\TH:i:s");
        $this->assertTrue($this->date->equals($sameDateOtherFormat));
        $differentDate = Date::fromNative("2017-08-10");
        $this->assertFalse($this->date->equals($differentDate));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue(Date::makeEmpty()->isEmpty());
        $this->assertFalse($this->date->isEmpty());
    }

    public function testToString(): void
    {
        $this->assertEquals(self::DATE, (string)$this->date);
        $this->assertEquals("", (string)Date::makeEmpty());
    }

    public function testGetOriginalFormat(): void
    {
        $this->assertEquals(Date::NATIVE_FORMAT, $this->date->getOriginalFormat());
        $this->assertEquals(
            "Y-m-d\\TH:i:s",
            Date::createFromString("2016-07-04T19:27:07", "Y-m-d\\TH:i:s")->getOriginalFormat()
        );
    }

    protected function setUp(): void
    {
        $this->date = Date::fromNative(self::DATE);
    }
}
