<?php

namespace Trellis\Runtime\Attribute\GeoPoint;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\ValueHolder\ComplexValue;
use stdClass;

class GeoPoint extends ComplexValue
{
    const PROPERTY_LONGITUDE = 'lon';
    const PROPERTY_LATITUDE = 'lat';

    protected $values = [
        self::PROPERTY_LONGITUDE => 0.0,
        self::PROPERTY_LATITUDE => 0.0
    ];

    public static function getMandatoryPropertyNames()
    {
        return [
            self::PROPERTY_LONGITUDE,
            self::PROPERTY_LATITUDE
        ];
    }

    public static function getPropertyMap()
    {
        return [
            self::PROPERTY_LONGITUDE => self::VALUE_TYPE_FLOAT,
            self::PROPERTY_LATITUDE => self::VALUE_TYPE_FLOAT
        ];
    }

    /**
     * Creates a new instance.
     *
     * @param array $data key value pairs to create the value object from
     */
    public function __construct(array $data)
    {
        // check for mandatory property values
        foreach ($this->getMandatoryPropertyNames() as $name) {
            if (!array_key_exists($name, $data)) {
                throw new BadValueException('No "' . $name . '" property given.');
            }
        }

        $name = self::PROPERTY_LONGITUDE;
        $float = filter_var($data[$name], FILTER_VALIDATE_FLOAT);
        if ($float === false || $data[$name] === true) {
            throw new BadValueException('Property "' . $name . '" must be a float value.');
        } else {
            if ($float > 180 || $float < -180) {
                throw new BadValueException('Property "' . $name . '" must be a float [-180…180] value.');
            }
            $this->values[$name] = $float;
        }

        $name = self::PROPERTY_LATITUDE;
        $float = filter_var($data[$name], FILTER_VALIDATE_FLOAT);
        if ($float === false || $data[$name] === true) {
            throw new BadValueException('Property "' . $name . '" must be a float value.');
        } else {
            if ($float > 90 || $float < -90) {
                throw new BadValueException('Property "' . $name . '" must be a float [-90…90] value.');
            }
            $this->values[$name] = $float;
        }
    }

    public function getLongitude()
    {
        return $this->values[self::PROPERTY_LONGITUDE];
    }

    public function getLatitude()
    {
        return $this->values[self::PROPERTY_LATITUDE];
    }

    public function getLon()
    {
        return $this->values[self::PROPERTY_LONGITUDE];
    }

    public function getLat()
    {
        return $this->values[self::PROPERTY_LATITUDE];
    }

    /**
     * @return GeoPoint location where the Equator and the Prime Meridian cross each other: (0,0)
     */
    public static function createNullIsland()
    {
        return new self([
            self::PROPERTY_LONGITUDE => 0.0,
            self::PROPERTY_LATITUDE => 0.0
        ]);
    }

    /**
     * Is this GeoPoint in the middle of the Atlantic Ocean, in the Gulf of Guinea off the coast of western Africa?
     * If yes, the location of this GeoPoint might be marked with a PIRATA weather observation buoy. Otherwise it's
     * most probably a non-fictional and valid coordinate not being used for flagging geocode failures.
     *
     * @see http://www.naturalearthdata.com/blog/natural-earth-version-1-3-release-notes/
     *
     * @return boolean whether this GeoPoint is on the indeterminate sovereignty class called Null Island
     */
    public function isNullIsland()
    {
        return ($this->values[self::PROPERTY_LONGITUDE] === 0.0) && ($this->values[self::PROPERTY_LATITUDE] === 0.0);
    }

    /**
     * @return array [ 'lon' => lon, 'lat' => lat ]
     */
    public function toNative()
    {
        return $this->values;
    }

    /**
     * @return array [ 'lon' => lon, 'lat' => lat ]
     */
    public function toArray()
    {
        return $this->values;
    }

    /**
     * @return array [lon, lat]
     */
    public function asGeoJsonPoint()
    {
        return [
            $this->getLongitude(),
            $this->getLatitude()
        ];
    }

    /**
     * @return string 'lat,lon'
     */
    public function asString()
    {
        return sprintf(
            '%g,%g',
            $this->getLatitude(),
            $this->getLongitude()
        );
    }
    /**
     * @return stdClass { lat:lat, lon:lon }
     */
    public function asObject()
    {
        $object = new stdClass;
        $object->lat = $this->getLatitude();
        $object->lon = $this->getLongitude();
        return $object;
    }

    /**
     * @return string 'lat,lon'
     */
    public function __toString()
    {
        return sprintf(
            '%g,%g',
            $this->getLatitude(),
            $this->getLongitude()
        );
    }
}
