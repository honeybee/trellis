<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\GeoPoint;
use Trellis\Tests\TestCase;

final class GeoPointTest extends TestCase
{
    public function testToNative(): void
    {
        $this->assertEquals(GeoPoint::EMPTY, (new GeoPoint)->toNative());
        $this->assertEquals([
            'lon' => 13.413215,
            'lat' => 52.521918
        ], (new GeoPoint(13.413215, 52.521918))->toNative());
    }

    public function testEquals(): void
    {
        $point = GeoPoint::fromArray([
            'lon' => 13.413215,
            'lat' => 52.521918
        ]);
        $this->assertTrue($point->equals(new GeoPoint(13.413215, 52.521918)));
        $this->assertFalse($point->equals(new GeoPoint(12.11716, 49.248)));
    }
}
