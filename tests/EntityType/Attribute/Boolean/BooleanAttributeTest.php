<?php

namespace Trellis\Tests\EntityType\Attribute\Boolean;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Boolean\BooleanAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Tests\TestCase;

class BooleanAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $bool_attribute = new BooleanAttribute('my_bool', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $bool_attribute);
        $this->assertEquals('my_bool', $bool_attribute->getName());
        $this->assertEquals($entity_type, $bool_attribute->getEntityType());
    }
}
