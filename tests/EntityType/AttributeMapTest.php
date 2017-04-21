<?php

namespace Trellis\Tests\EntityType;

use Trellis\EntityType\Attribute;
use Trellis\EntityType\AttributeMap;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Tests\TestCase;
use Trellis\ValueObject\GeoPoint;
use Trellis\ValueObject\Integer;
use Trellis\ValueObject\Text;

final class AttributeMapTest extends TestCase
{
    /**
     * @var AttributeMap
     */
    private $attributeMap;

    public function testGet()
    {
        $this->assertInstanceOf(Attribute::class, $this->attributeMap->get("id"));
        $this->assertInstanceOf(Attribute::class, $this->attributeMap->get("name"));
        $this->assertInstanceOf(Attribute::class, $this->attributeMap->get("location"));
    }

    public function testByClassNames()
    {
        $attributeMap = $this->attributeMap->byClassNames([ Attribute::class ]);
        $this->assertCount(3, $attributeMap);
    }

    public function testHas()
    {
        $this->assertTrue($this->attributeMap->has("id"));
        $this->assertTrue($this->attributeMap->has("name"));
        $this->assertTrue($this->attributeMap->has("location"));
        $this->assertFalse($this->attributeMap->has("foobar"));
    }

    public function testCount()
    {
        $this->assertCount(3, $this->attributeMap);
    }

    protected function setUp()
    {
        /* @var EntityTypeInterface $entityType */
        $entityType = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $this->attributeMap = new AttributeMap([
            Attribute::define("id", $entityType, Integer::class),
            Attribute::define("name", $entityType, Text::class),
            Attribute::define("location", $entityType, GeoPoint::class)
        ]);
    }
}
