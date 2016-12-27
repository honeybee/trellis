<?php

namespace Trellis\Tests\EntityType;

use Trellis\EntityType\Attribute\GeoPointAttribute;
use Trellis\EntityType\Attribute\IntegerAttribute;
use Trellis\EntityType\Attribute\TextAttribute;
use Trellis\EntityType\AttributeMap;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class AttributeMapTest extends TestCase
{
    /**
     * @var AttributeMap
     */
    private $attribute_map;

    public function testGet()
    {
        $this->assertInstanceOf(IntegerAttribute::CLASS, $this->attribute_map->get("id"));
        $this->assertInstanceOf(TextAttribute::CLASS, $this->attribute_map->get("name"));
        $this->assertInstanceOf(GeoPointAttribute::CLASS, $this->attribute_map->get("location"));
    }

    public function testByClassNames()
    {
        $attribute_map = $this->attribute_map->byClassNames([ TextAttribute::CLASS, GeoPointAttribute::CLASS ]);
        $this->assertCount(2, $attribute_map);
    }

    public function testHas()
    {
        $this->assertTrue($this->attribute_map->has("id"));
        $this->assertTrue($this->attribute_map->has("name"));
        $this->assertTrue($this->attribute_map->has("location"));
        $this->assertFalse($this->attribute_map->has("foobar"));
    }

    public function testCount()
    {
        $this->assertCount(3, $this->attribute_map);
    }

    protected function setUp()
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $this->attribute_map = new AttributeMap([
            new IntegerAttribute("id", $entity_type),
            new TextAttribute("name", $entity_type),
            new GeoPointAttribute("location", $entity_type)
        ]);
    }
}
