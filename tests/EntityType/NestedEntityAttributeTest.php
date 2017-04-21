<?php

namespace Trellis\Tests\EntityType;

use Trellis\EntityType\EntityTypeInterface;
use Trellis\EntityType\NestedEntityAttribute;
use Trellis\Tests\Fixture\Location;
use Trellis\Tests\Fixture\LocationType;
use Trellis\Tests\TestCase;
use Trellis\ValueObject\Nil;

final class NestedEntityAttributeTest extends TestCase
{
    private const FIXED_DATA = [
        "@type" => "location",
        "id" => 42,
        "name" => "my poi",
        "street" => "fleetstreet 23",
        "postal_code" => "1337",
        "city" => "codetown",
        "country" => "Utopia",
        "coords" => [ "lon" => 0.0, "lat" => 0.0 ]
    ];

    /**
     * @var NestedEntityAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertEquals(self::FIXED_DATA, $this->attribute->makeValue(self::FIXED_DATA)->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $locationType = $this->attribute->getAllowedTypes()->get("location");
        $location = Location::fromNative(self::FIXED_DATA, [ "entity_type" => $locationType ]);
        $this->assertEquals(self::FIXED_DATA, $this->attribute->makeValue($location)->toNative());
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertInstanceOf(Nil::class, $this->attribute->makeValue());
    }

    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue("snafu!");
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\MissingImplementation
     */
    public function testNonExistingTypeClass(): void
    {
        /* @var EntityTypeInterface $entityType */
        $entityType = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        NestedEntityAttribute::define("foo", $entityType, [ "\\Trellis\\FooBaR" ]);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\CorruptValues
     */
    public function testInvalidType(): void
    {
        $data = self::FIXED_DATA;
        $data["@type"] = "foobar";
        $this->attribute->makeValue($data);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testMissingType(): void
    {
        $data = self::FIXED_DATA;
        unset($data["@type"]);
        $this->attribute->makeValue($data);
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entityType */
        $entityType = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $this->attribute = NestedEntityAttribute::define("locations", $entityType, [ LocationType::CLASS ]);
    }
}
