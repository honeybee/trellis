<?php

namespace Trellis\Tests\EntityType\Attribute\Decimal;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Decimal\Decimal;
use Trellis\EntityType\Attribute\Decimal\DecimalAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Tests\TestCase;

class DecimalAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $float_attribute = new DecimalAttribute('my_float', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $float_attribute);
        $this->assertEquals('my_float', $float_attribute->getName());
        $this->assertEquals($entity_type, $float_attribute->getEntityType());
    }

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $float_attribute = new DecimalAttribute('my_float', $entity_type);

        $this->assertInstanceOf(Decimal::CLASS, $float_attribute->createValue());
        $this->assertInstanceOf(Decimal::CLASS, $float_attribute->createValue(new Decimal(42.5)));
        $this->assertInstanceOf(Decimal::CLASS, $float_attribute->createValue(5.23));
    }
}
