<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Date;
use Trellis\EntityType\Attribute\DateAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class DateAttributeTest extends TestCase
{
    /**
     * @var DateAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertEquals("2016-07-04", $this->attribute->makeValue("2016-07-04")->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $date = $this->attribute->makeValue(Date::createFromString("2016-07-04"));
        $this->assertEquals("2016-07-04", $date->toNative());
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertEquals(Date::EMPTY, $this->attribute->makeValue()->toNative());
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
        $this->attribute = new DateAttribute("date", $entity_type);
    }
}
