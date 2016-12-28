<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Timestamp;
use Trellis\Tests\TestCase;

final class TimestampTest extends TestCase
{
    private const FIXED_TIMESTAMP_EUR = "2016-07-04T19:27:07.000000+02:00";

    private const FIXED_TIMESTAMP_UTC = "2016-07-04T17:27:07.000000+00:00";

    /**
     * @var Timestamp $timestamp
     */
    private $timestamp;

    public function testToNative(): void
    {
        $this->assertEquals(self::FIXED_TIMESTAMP_UTC, $this->timestamp->toNative());
        $this->assertEquals(Timestamp::EMPTY, (new Timestamp)->toNative());
    }

    public function testEquals(): void
    {
        $equal_ts = Timestamp::createFromString("2016-07-04T17:27:07", "Y-m-d\\TH:i:s");
        $this->assertTrue($this->timestamp->equals($equal_ts));
        $different_ts = Timestamp::createFromString("2017-08-04T17:27:07", "Y-m-d\\TH:i:s");
        $this->assertFalse($this->timestamp->equals($different_ts));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue((new Timestamp)->isEmpty());
        $this->assertFalse($this->timestamp->isEmpty());
    }

    public function testGetOriginalFormat(): void
    {
        $format = Timestamp::createFromString("2017-08-04T19:27:07", "Y-m-d\\TH:i:s")->getOriginalFormat();
        $this->assertEquals("Y-m-d\\TH:i:s", $format);
    }

    public function testToString()
    {
        $this->assertEquals(self::FIXED_TIMESTAMP_UTC, (string)$this->timestamp);
    }

    protected function setUp(): void
    {
        $this->timestamp = new Timestamp(new \DateTimeImmutable(self::FIXED_TIMESTAMP_EUR));
    }
}
