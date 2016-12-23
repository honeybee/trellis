<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\EntityType\Attribute\GeoPointAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class GeoPointAttributeTest extends TestCase
{
    public function testMakeValue(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $attribute = new GeoPointAttribute('location', $entity_type);
        $wgs84 = ['lon' => 13.413215, 'lat' => 52.521918 ];
        $this->assertEquals($wgs84, $attribute->makeValue($wgs84)->toNative());
    }
}
