<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\GeoPoint;
use Trellis\EntityType\Attribute\GeoPointAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class GeoPointAttributeTest extends TestCase
{
    /**
     * @var GeoPointAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $wgs84 = [ "lon" => 13.413215, "lat" => 52.521918 ];
        $this->assertEquals($wgs84, $this->attribute->makeValue($wgs84)->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $wgs84 = [ "lon" => 13.413215, "lat" => 52.521918 ];
        $this->assertEquals($wgs84, $this->attribute->makeValue(GeoPoint::fromArray($wgs84))->toNative());
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertEquals(GeoPoint::EMPTY, $this->attribute->makeValue()->toNative());
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue("wont work");
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $this->attribute = new GeoPointAttribute("location", $entity_type);
    }
}
