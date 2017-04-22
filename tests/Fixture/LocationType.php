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
            Attribute::define("id", Integer::class, $this),
            Attribute::define("name", Text::class, $this),
            Attribute::define("street", Text::class, $this),
            Attribute::define("postal_code", Text::class, $this),
            Attribute::define("city", Text::class, $this),
            Attribute::define("country", Text::class, $this),
            Attribute::define("coords", GeoPoint::class, $this)
        ], $parentAttribute);
    }

    /**
     * @inheritDoc
     */
    public function makeEntity(array $entityState = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        $entityState["@type"] = $this;
        $entityState["@parent"] = $parent;
        return Location::fromArray($entityState);
    }
}
