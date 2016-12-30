<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Nil;
use Trellis\EntityType\Attribute\NestedEntityAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\Fixture\Location;
use Trellis\Tests\Fixture\LocationType;
use Trellis\Tests\TestCase;

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
        $location = new Location($this->attribute->getEntityTypeMap()->get("location"), self::FIXED_DATA);
        $this->assertEquals(self::FIXED_DATA, $this->attribute->makeValue($location)->toNative());
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertInstanceOf(Nil::CLASS, $this->attribute->makeValue());
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
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
        $params = [ NestedEntityAttribute::PARAM_TYPES => [ "\\Trellis\\FooBaR" ] ];
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        new NestedEntityAttribute("foo", $entity_type, $params);
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
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $params = [ NestedEntityAttribute::PARAM_TYPES => [ LocationType::CLASS ] ];
        $this->attribute = new NestedEntityAttribute("locations", $entity_type, $params);
    }
}
