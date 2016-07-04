<?php

namespace Trellis\Tests\Attribute\Boolean;

use Trellis\Attribute\AttributeInterface;
use Trellis\Attribute\Boolean\BooleanAttribute;
use Trellis\Entity\EntityTypeInterface;
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
