<?php

namespace Trellis\Tests\Attribute\Integer;

use Trellis\Attribute\AttributeInterface;
use Trellis\Attribute\Integer\IntegerAttribute;
use Trellis\Entity\EntityTypeInterface;
use Trellis\Tests\TestCase;

class IntegerAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $int_attribute = new IntegerAttribute('my_int', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $int_attribute);
        $this->assertEquals('my_int', $int_attribute->getName());
        $this->assertEquals($entity_type, $int_attribute->getEntityType());
    }
}
