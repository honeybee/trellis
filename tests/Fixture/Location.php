<?php

namespace Trellis\Tests\Fixture;

use Trellis\Entity\NestedEntity;
use Trellis\ValueObject\GeoPoint;
use Trellis\ValueObject\Integer;
use Trellis\ValueObject\Text;
use Trellis\ValueObject\ValueObjectInterface;

final class Location extends NestedEntity
{
    /**
     * @return ValueObjectInterface
     */
    public function getIdentity(): ValueObjectInterface
    {
        return $this->getId();
    }

    /**
     * @return Integer
     */
    public function getId(): Integer
    {
        return $this->get("id");
    }

    /**
     * @return Text
     */
    public function getName(): Text
    {
        return $this->get("name");
    }

    /**
     * @return Text
     */
    public function getStreet(): Text
    {
        return $this->get("street");
    }

    /**
     * @return Text
     */
    public function getPostalCode(): Text
    {
        return $this->get("postal_code");
    }

    public function getCity(): Text
    {
        return $this->get("city");
    }

    /**
     * @return Text
     */
    public function getCountry(): Text
    {
        return $this->get("country");
    }

    /**
     * @return GeoPoint
     */
    public function getCoords(): GeoPoint
    {
        return $this->get("coords");
    }
}
