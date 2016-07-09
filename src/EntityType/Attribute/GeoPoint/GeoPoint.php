<?php

namespace Trellis\EntityType\Attribute\GeoPoint;

use Assert\Assertion;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Exception;

class GeoPoint implements ValueInterface
{
    use NativeEqualsComparison;

    const NIL = 0.0;

    /**
     * @var float $lon
     */
    private $lon;

    /**
     * @var float $lat
     */
    private $lat;

    public static function fromArray(array $point)
    {
        Assertion::keyExists($point, 'lon');
        Assertion::keyExists($point, 'lat');

        return new static($point['lon'], $point['lat']);
    }

    /**
     * @param string $geo_point
     */
    public function __construct($lon = self::NIL, $lat = self::NIL)
    {
        Assertion::float($lon, "GeoPoint.lon must be a float.");
        Assertion::float($lat, "GeoPoint.lat must be a float.");

        $this->lon = $lon;
        $this->lat = $lat;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->lon === self::NIL && $this->lat === self::NIL;
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return [ 'lon' => $this->getLon(), 'lat' => $this->getLat() ];
    }

    public function getLon()
    {
        return $this->lon;
    }

    public function getLat()
    {
        return $this->lat;
    }
}
