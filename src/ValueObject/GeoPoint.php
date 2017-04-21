<?php

namespace Trellis\ValueObject;

use Trellis\Assert\Assertion;

final class GeoPoint implements ValueObjectInterface
{
    public const NULL_ISLAND = [
        "lon" => 0.0,
        "lat" => 0.0
    ];

    /**
     * @var Decimal
     */
    private $lon;

    /**
     * @var Decimal
     */
    private $lat;

    /**
     * {@inheritdoc}
     */
    public static function fromNative($nativeValue): ValueObjectInterface
    {
        return $nativeValue ? self::fromArray($nativeValue) : self::makeEmpty();
    }

    /**
     * {@inheritdoc}
     */
    public static function makeEmpty(): ValueObjectInterface
    {
        return new static(self::NULL_ISLAND["lon"], self::NULL_ISLAND["lat"]);
    }

    /**
     * @param float[] $point
     *
     * @return GeoPoint
     */
    public static function fromArray(array $point): GeoPoint
    {
        Assertion::keyExists($point, "lon");
        Assertion::keyExists($point, "lat");
        return new static($point["lon"], $point["lat"]);
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObjectInterface $otherValue): bool
    {
        Assertion::isInstanceOf($otherValue, GeoPoint::class);
        return $this->toNative() == $otherValue->toNative();
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->toNative() == self::NULL_ISLAND;
    }

    /**
     * @return float[]
     */
    public function toNative(): array
    {
        return [
            "lon" => $this->lon->toNative(),
            "lat" => $this->lat->toNative()
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
        return sprintf("lon: %s, lat: %s", $this->lon, $this->lat);
    }

    /**
     * @param float $lon
     * @param float $lat
     */
    private function __construct(float $lon, float $lat)
    {
        $this->lon = Decimal::fromNative($lon);
        $this->lat = Decimal::fromNative($lat);
    }
}
