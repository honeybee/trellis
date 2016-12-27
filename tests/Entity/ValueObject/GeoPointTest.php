<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\GeoPoint;
use Trellis\Tests\TestCase;

final class GeoPointTest extends TestCase
{
    const COORDS = [
        'lon' => 13.413215,
        'lat' => 52.521918
    ];

    /**
     * @var GeoPoint $geo_point
     */
    private $geo_point;

    public function testToNative(): void
    {
        $this->assertEquals(GeoPoint::EMPTY, (new GeoPoint)->toNative());
        $this->assertEquals(self::COORDS, $this->geo_point->toNative());
    }

    public function testEquals(): void
    {
        $same_point = new GeoPoint(self::COORDS["lon"], self::COORDS["lat"]);
        $this->assertTrue($this->geo_point->equals($same_point));
        $different_point = new GeoPoint(12.11716, 49.248);
        $this->assertFalse($this->geo_point->equals($different_point));
    }

    public function testIsEmpty(): void
    {
        $this->assertFalse($this->geo_point->isEmpty());
        $this->assertTrue((new GeoPoint)->isEmpty());
    }

    public function testGetLon()
    {
        $this->assertEquals(self::COORDS["lon"], $this->geo_point->getLon()->toNative());
    }

    public function testGetLat()
    {
        $this->assertEquals(self::COORDS["lat"], $this->geo_point->getLat()->toNative());
    }

    public function testToString()
    {
        $this->assertEquals(
            sprintf("lon: %f, lat: %f", self::COORDS["lon"], self::COORDS["lat"]),
            (string)$this->geo_point
        );
    }

    protected function setUp()
    {
        $this->geo_point = GeoPoint::fromArray(self::COORDS);
    }
}
