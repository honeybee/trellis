<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\EntityList;
use Trellis\EntityInterface;
use Trellis\EntityType\Attribute\EntityListAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\Location;
use Trellis\Tests\Fixture\LocationType;
use Trellis\Tests\TestCase;

final class EntityListAttributeTest extends TestCase
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
     * @var EntityListAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $locations = $this->attribute->makeValue(self::FIXED_DATA);
        $this->assertEquals(self::FIXED_DATA, $locations->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $parent = $this->getMockBuilder(EntityInterface::CLASS)->getMock();
        $location_type = $this->attribute->getEntityTypeMap()->get('location');
        $locations = new EntityList([ new Location($location_type, self::FIXED_DATA[0])], $parent);
        $this->assertEquals(self::FIXED_DATA, $this->attribute->makeValue($locations)->toNative());
    }

    /**
     * @expectedException \Trellis\Error\MissingImplementation
     */
    public function testNonExistingTypeClass(): void
    {
        $params = [ EntityListAttribute::OPTION_TYPES => [ "\\Trellis\\FooBaR" ] ];
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        new EntityListAttribute("foo", $entity_type, $params);
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
     * @expectedException \Trellis\Error\CorruptValues
     */
    public function testMissingType(): void
    {
        $data = self::FIXED_DATA;
        unset($data[0]["@type"]);
        $this->attribute->makeValue($data);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue(5);
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $params = [ EntityListAttribute::OPTION_TYPES => [ LocationType::CLASS ] ];
        $this->attribute = new EntityListAttribute("locations", $entity_type, $params);
    }
}
