<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityType\Attribute;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\EntityType;
use Trellis\Entity\TypedEntityInterface;
use Trellis\ValueObject\GeoPoint;
use Trellis\ValueObject\Integer;
use Trellis\ValueObject\Text;

final class LocationType extends EntityType
{
    /**
     * @param AttributeInterface $parentAttribute
     */
    public function __construct(AttributeInterface $parentAttribute)
    {
        parent::__construct("Location", [
            Attribute::define("id", $this, Integer::class),
            Attribute::define("name", $this, Text::class),
            Attribute::define("street", $this, Text::class),
            Attribute::define("postal_code", $this, Text::class),
            Attribute::define("city", $this, Text::class),
            Attribute::define("country", $this, Text::class),
            Attribute::define("coords", $this, GeoPoint::class)
        ], $parentAttribute);
    }

    /**
     * @inheritDoc
     */
    public function makeEntity(array $data = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        return Location::fromNative($data, [ "entity_type" => $this, "parent" => $parent ]);
    }
}
