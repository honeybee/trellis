<?php

namespace Trellis\Tests\EntityType;

use Trellis\EntityType\EntityTypeInterface;
use Trellis\EntityType\NestedEntityListAttribute;
use Trellis\Entity\EntityInterface;
use Trellis\Entity\NestedEntityList;
use Trellis\Entity\TypedEntityInterface;
use Trellis\Tests\Fixture\Location;
use Trellis\Tests\Fixture\LocationType;
use Trellis\Tests\TestCase;

final class NestedEntityListAttributeTest extends TestCase
{
    private const FIXED_DATA = [ [
        "@type" => "location",
        "id" => 42,
        "name" => "my poi",
        "street" => "fleetstreet 23",
        "postal_code" => "1337",
        "city" => "codetown",
        "country" => "Utopia",
        "coords" => [ "lon" => 0.0, "lat" => 0.0 ]
    ] ];

    /**
     * @var NestedEntityListAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $locations = $this->attribute->makeValue(self::FIXED_DATA);
        $this->assertEquals(self::FIXED_DATA, $locations->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $parent = $this->getMockBuilder(TypedEntityInterface::CLASS)->getMock();
        $locationType = $this->attribute->getAllowedTypes()->get("location");
        $locations = new NestedEntityList([ Location::fromNative(self::FIXED_DATA[0], [
            "entity_type" => $locationType,
            "parent" => $parent
        ])], $parent);
        $this->assertEquals(self::FIXED_DATA, $this->attribute->makeValue($locations)->toNative());
    }

    /**
     * @expectedException \Trellis\Error\MissingImplementation
     */
    public function testNonExistingTypeClass(): void
    {
        /* @var EntityTypeInterface $entityType */
        $entityType = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        NestedEntityListAttribute::define("foo", $entityType, [ "\\Trellis\\FooBaR" ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\CorruptValues
     */
    public function testInvalidType(): void
    {
        $data = self::FIXED_DATA;
        $data[0]["@type"] = "foobar";
        $this->attribute->makeValue($data);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testMissingType(): void
    {
        $data = self::FIXED_DATA;
        unset($data[0]["@type"]);
        $this->attribute->makeValue($data);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue(5);
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entityType */
        $entityType = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $this->attribute = NestedEntityListAttribute::define("locations", $entityType, [ LocationType::CLASS ]);
    }
}
