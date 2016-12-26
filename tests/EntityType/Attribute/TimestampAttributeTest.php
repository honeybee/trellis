<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Timestamp;
use Trellis\EntityType\Attribute\TimestampAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class TimestampAttributeTest extends TestCase
{
    /**
     * @var TimestampAttribute $attribute
     */
    private $attribute;

    public function testMakeValue(): void
    {
        $this->assertEquals(
            "2016-07-04T19:27:07.000000+02:00",
            $this->attribute->makeValue("2016-07-04T19:27:07.000000+02:00")->toNative()
        );
    }

    public function testMakeValueFromNative(): void
    {
        $timestamp = $this->attribute->makeValue("2016-07-04T19:27:07.000000+02:00");
        $this->assertEquals("2016-07-04T19:27:07.000000+02:00", $timestamp->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $timestamp = Timestamp::createFromString("2016-07-04T19:27:07.000000+02:00");
        $this->assertEquals("2016-07-04T19:27:07.000000+02:00", $this->attribute->makeValue($timestamp)->toNative());
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertEquals(Timestamp::EMPTY, $this->attribute->makeValue()->toNative());
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue(23);
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $this->attribute = new TimestampAttribute("timestmap", $entity_type);
    }
}
