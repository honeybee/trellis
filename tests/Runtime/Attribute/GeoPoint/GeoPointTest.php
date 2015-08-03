<?php

namespace Trellis\Tests\Runtime\Attribute\GeoPoint;

use InvalidArgumentException;
use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\GeoPoint\GeoPoint;
use Trellis\Tests\TestCase;

class GeoPointTest extends TestCase
{
    public function testSimpleCreateSucceeds()
    {
        $coords = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 12.34,
            GeoPoint::PROPERTY_LATITUDE => 53.21
        ]);
        $this->assertEquals($coords->getLongitude(), 12.34);
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     */
    public function testSimpleCreateFailsWithEmptyString()
    {
        $gp = new GeoPoint([ GeoPoint::PROPERTY_LONGITUDE => '', GeoPoint::PROPERTY_LATITUDE => '' ]);
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     */
    public function testCreateWithoutArgumentsFails()
    {
        $gp = new GeoPoint([]);
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     */
    public function testCreateFromPartialArrayFails()
    {
        $gp = GeoPoint::createFromArray([
            GeoPoint::PROPERTY_LONGITUDE => 12.34
        ]);
    }

    public function testCreateWithZeroCoordsSucceeds()
    {
        $gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 0,
            GeoPoint::PROPERTY_LATITUDE => 0
        ]);
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     */
    public function testInvalidMinLongitudeRejected()
    {
        $gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => -180.1,
            GeoPoint::PROPERTY_LATITUDE => 0
        ]);
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     */
    public function testInvalidMaxLongitudeRejected()
    {
        $gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 180.1,
            GeoPoint::PROPERTY_LATITUDE => 0
        ]);
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     */
    public function testInvalidMinLatitudeRejected()
    {
        $gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 123,
            GeoPoint::PROPERTY_LATITUDE => -90.01
        ]);
    }

    /**
     * @expectedException Trellis\Common\Error\BadValueException
     */
    public function testInvalidMaxLatitudeRejected()
    {
        $gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 123.1,
            GeoPoint::PROPERTY_LATITUDE => 90.01
        ]);
    }


    public function testComparisonOfTwoSimilarGeoPointsSucceeds()
    {
        $other_gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 12.34,
            GeoPoint::PROPERTY_LATITUDE => 53.21
        ]);

        $gp = GeoPoint::createFromArray($other_gp->toNative());

        $this->assertEquals(12.34, $gp->getLongitude());
        $this->assertEquals(53.21, $gp->getLatitude());

        $this->assertTrue($gp->similarTo($other_gp));
    }

    public function testCreateWithSucceeds()
    {
        $gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 12.34,
            GeoPoint::PROPERTY_LATITUDE => 53.21
        ]);

        $diff_gp = $gp->createWith([
            GeoPoint::PROPERTY_LONGITUDE => -150.1234
        ]);

        $this->assertEquals(-150.1234, $diff_gp->getLongitude());
        $this->assertEquals(53.21, $diff_gp->getLatitude());
        $this->assertFalse($gp->similarTo($diff_gp));
        $this->assertFalse($diff_gp->similarTo($gp));
    }

    public function testAsObjectSucceeds()
    {
        $gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 12.34,
            GeoPoint::PROPERTY_LATITUDE => 53.21
        ]);

        $this->assertInstanceOf('stdClass', $gp->asObject());
        $this->assertEquals('{"lat":53.21,"lon":12.34}', json_encode($gp->asObject()));
    }

    public function testAsStringSucceeds()
    {
        $gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 12.34,
            GeoPoint::PROPERTY_LATITUDE => 53.21
        ]);

        $this->assertEquals('53.21,12.34', $gp->asString());
        $this->assertEquals('53.21,12.34', (string)$gp);
    }

    public function testAsGeoJsonPointSucceeds()
    {
        $gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 12.34,
            GeoPoint::PROPERTY_LATITUDE => 53.21
        ]);

        $this->assertEquals([12.34,53.21], $gp->asGeoJsonPoint());
    }

    public function testToArrayValuesEqualToNative()
    {
        $gp = new GeoPoint([
            GeoPoint::PROPERTY_LONGITUDE => 12.34,
            GeoPoint::PROPERTY_LATITUDE => 53.21
        ]);

        $a = $gp->toArray();
        $b = $gp->toNative();

        $this->assertEquals($a, $b);
    }
}
