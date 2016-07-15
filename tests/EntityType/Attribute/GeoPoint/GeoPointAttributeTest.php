<?php

namespace Trellis\Tests\EntityType\Attribute\GeoPoint;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\GeoPoint\GeoPoint;
use Trellis\EntityType\Attribute\GeoPoint\GeoPointAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Tests\TestCase;

class GeoPointAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $geo_point_attribute = new GeoPointAttribute('my_geo_point', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $geo_point_attribute);
        $this->assertEquals('my_geo_point', $geo_point_attribute->getName());
        $this->assertEquals($entity_type, $geo_point_attribute->getEntityType());
    }

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $geo_point_attribute = new GeoPointAttribute('my_geo_point', $entity_type);

        $this->assertInstanceOf(GeoPoint::CLASS, $geo_point_attribute->createValue());
        $this->assertInstanceOf(
            GeoPoint::CLASS,
            $geo_point_attribute->createValue(new GeoPoint(12.35, 15.23))
        );
        $this->assertInstanceOf(
            GeoPoint::CLASS,
            $geo_point_attribute->createValue([ 'lon' => 11.234, 'lat' => 12.345 ])
        );
    }
}
