<?php

namespace Trellis\Tests\ValueObject;

use Trellis\Tests\TestCase;
use Trellis\ValueObject\GeoPoint;

final class GeoPointTest extends TestCase
{
    private const COORDS = [
        "lon" => 13.413215,
        "lat" => 52.521918
    ];

    /**
     * @var GeoPoint
     */
    private $geoPoint;

    public function testToNative(): void
    {
        $this->assertEquals(GeoPoint::NULL_ISLAND, GeoPoint::makeEmpty()->toNative());
        $this->assertEquals(self::COORDS, $this->geoPoint->toNative());
    }

    public function testEquals(): void
    {
        $samePoint = GeoPoint::fromNative(self::COORDS);
        $this->assertTrue($this->geoPoint->equals($samePoint));
        $differentPoint = GeoPoint::fromNative([ "lon" => 12.11716, "lat" => 49.248 ]);
        $this->assertFalse($this->geoPoint->equals($differentPoint));
    }

    public function testIsEmpty(): void
    {
        $this->assertFalse($this->geoPoint->isEmpty());
        $this->assertTrue(GeoPoint::makeEmpty()->isEmpty());
    }

    public function testGetLon()
    {
        $this->assertEquals(self::COORDS["lon"], $this->geoPoint->getLon()->toNative());
    }

    public function testGetLat()
    {
        $this->assertEquals(self::COORDS["lat"], $this->geoPoint->getLat()->toNative());
    }

    public function testToString()
    {
        $this->assertEquals(
            sprintf("lon: %f, lat: %f", self::COORDS["lon"], self::COORDS["lat"]),
            (string)$this->geoPoint
        );
    }

    protected function setUp()
    {
        $this->geoPoint = GeoPoint::fromNative(self::COORDS);
    }
}
