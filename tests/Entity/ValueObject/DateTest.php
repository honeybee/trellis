<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\Date;
use Trellis\Entity\ValueObject\Timestamp;
use Trellis\Tests\TestCase;

final class DateTest extends TestCase
{
    private const DATE = "2016-07-04";

    /**
     * @var Date $date
     */
    private $date;

    public function testToNative(): void
    {
        $this->assertEquals(self::DATE, $this->date->toNative());
        $this->assertEquals(Timestamp::EMPTY, (new Date)->toNative());
    }

    public function testEquals(): void
    {
        $same_date = Date::createFromString(self::DATE);
        $this->assertTrue($this->date->equals($same_date));
        $same_date_other_format = Date::createFromString("2016-07-04T19:27:07", "Y-m-d\\TH:i:s");
        $this->assertTrue($this->date->equals($same_date_other_format));
        $different_date = Date::createFromString("2017-08-10");
        $this->assertFalse($this->date->equals($different_date));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue((new Date)->isEmpty());
        $this->assertFalse($this->date->isEmpty());
    }

    public function testToString(): void
    {
        $this->assertEquals(self::DATE, (string)$this->date);
        $this->assertEquals(Date::EMPTY, (string)new Date);
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
        $this->date = new Date(new \DateTimeImmutable(self::DATE));
    }
}
