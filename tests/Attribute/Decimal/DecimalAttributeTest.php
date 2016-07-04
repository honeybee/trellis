<?php

namespace Trellis\Tests\Attribute\Decimal;

use Trellis\Attribute\AttributeInterface;
use Trellis\Attribute\Decimal\DecimalAttribute;
use Trellis\Entity\EntityTypeInterface;
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
}
