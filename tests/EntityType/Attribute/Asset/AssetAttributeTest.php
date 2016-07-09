<?php

namespace Trellis\Tests\EntityType\Attribute\Asset;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Asset\Asset;
use Trellis\EntityType\Attribute\Asset\AssetAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Entity\EntityInterface;
use Trellis\Tests\TestCase;

class AssetAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $asset_attribute = new AssetAttribute('my_asset', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $asset_attribute);
        $this->assertEquals('my_asset', $asset_attribute->getName());
        $this->assertEquals($entity_type, $asset_attribute->getEntityType());
    }

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $asset_attribute = new AssetAttribute('my_asset', $entity_type);

        $this->assertInstanceOf(Asset::CLASS, $asset_attribute->createValue($entity));
        $this->assertInstanceOf(
            Asset::CLASS,
            $asset_attribute->createValue($entity, new Asset(__FILE__))
        );
        $this->assertInstanceOf(Asset::CLASS, $asset_attribute->createValue($entity, [ 'location' => __FILE__ ]));
    }
}
