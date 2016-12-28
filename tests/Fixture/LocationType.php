<?php

namespace Trellis\Tests\Fixture;

use Trellis\EntityType\Attribute\GeoPointAttribute;
use Trellis\EntityType\Attribute\IntegerAttribute;
use Trellis\EntityType\Attribute\TextAttribute;
use Trellis\EntityType\AttributeInterface;
use Trellis\EntityType\AttributeMap;
use Trellis\EntityType\EntityType;
use Trellis\EntityType\Params;
use Trellis\TypedEntityInterface;

final class LocationType extends EntityType
{
    /**
     * @param AttributeInterface $parent_attribute
     */
    public function __construct(AttributeInterface $parent_attribute)
    {
        parent::__construct(
            "Location",
            new AttributeMap([
                new IntegerAttribute("id", $this),
                new TextAttribute("name", $this),
                new TextAttribute("street", $this),
                new TextAttribute("postal_code", $this),
                new TextAttribute("city", $this),
                new TextAttribute("country", $this),
                new GeoPointAttribute("coords", $this)
            ]),
            new Params([ "prefix" => "location" ]),
            $parent_attribute
        );
    }

    /**
     * @param mixed[] $data Optional data for initial hydration.
     * @param TypedEntityInterface $parent
     *
     * @return TypedEntityInterface
     */
    public function makeEntity(array $data = [], TypedEntityInterface $parent = null): TypedEntityInterface
    {
        return new Location($this, $data, $parent);
    }
}
