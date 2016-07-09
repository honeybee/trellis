<?php

namespace Trellis\Tests\EntityType\Attribute\GeoPoint;

use Trellis\EntityType\Attribute\GeoPoint\GeoPoint;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

class GeoPointTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new GeoPoint(12.345, 13.456));
    }

    public function testToNative()
    {
        $geo_point = new GeoPoint(12.345, 13.456);
        $this->assertEquals([ 'lon' => 12.345, 'lat' => 13.456 ], $geo_point->toNative());
        $geo_point = new GeoPoint;
        $this->assertEquals([ 'lon' => 0.0, 'lat' => 0.0 ], $geo_point->toNative());
    }

    public function testIsEmpty()
    {
        $geo_point = new GeoPoint(12.345, 13.456);
        $this->assertFalse($geo_point->isEmpty());
        $geo_point = new GeoPoint;
        $this->assertTrue($geo_point->isEmpty());
        $geo_point = new GeoPoint(0.0, 0.0);
        $this->assertTrue($geo_point->isEmpty());
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueString()
    {
        new GeoPoint('hello world!');
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueInt()
    {
        new GeoPoint(23);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValueBool()
    {
        new GeoPoint(true);
    } // @codeCoverageIgnore
}
