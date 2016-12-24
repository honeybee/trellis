<?php

namespace Trellis\Entity\ValueObject;

use Trellis\Entity\ValueObjectInterface;
use Trellis\Error\Assert\Assertion;

final class GeoPoint implements ValueObjectInterface
{
    const EMPTY = [
        'lon' => 0.0,
        'lat' => 0.0
    ];

    /**
     * @var Decimal $lon
     */
    private $lon;

    /**
     * @var Decimal $lat
     */
    private $lat;

    /**
     * @param float[] $point
     *
     * @return GeoPoint
     */
    public static function fromArray(array $point): GeoPoint
    {
        Assertion::keyExists($point, 'lon');
        Assertion::keyExists($point, 'lat');
        return new GeoPoint($point['lon'], $point['lat']);
    }

    /**
     * @param float $lon
     * @param float $lat
     */
    public function __construct(float $lon = 0.0, float $lat = 0.0)
    {
        $this->lon = new Decimal($lon);
        $this->lat = new Decimal($lat);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $other_value): bool
    {
        Assertion::isInstanceOf($other_value, GeoPoint::CLASS);
        return $this->toNative() === $other_value->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->toNative() === self::EMPTY;
    }

    /**
     * @return float[]
     */
    public function toNative(): array
    {
        return [
            'lon' => $this->lon->toNative(),
            'lat' => $this->lat->toNative()
        ];
    }

    /**
     * @return Decimal
     */
    public function getLon(): Decimal
    {
        return $this->lon;
    }

    /**
     * @return Decimal
     */
    public function getLat(): Decimal
    {
        return $this->lat;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf("lon: %f, lat: %f", $this->lon, $this->lat);
    }
}
