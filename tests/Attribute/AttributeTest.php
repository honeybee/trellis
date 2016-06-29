<?php

namespace Trellis\Tests\Attribute;

use Trellis\Attribute\Attribute;
use Trellis\Attribute\AttributeInterface;
use Trellis\Entity\EntityTypeInterface;
use Trellis\Tests\TestCase;

class AttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entityType = $this->getMockBuilder(EntityTypeInterface::class)->getMock();

        $attribute = new Attribute('foobar', $entityType);

        $this->assertInstanceOf(AttributeInterface::CLASS, $attribute);
        $this->assertEquals('foobar', $attribute->getName());
        $this->assertEquals($entityType, $attribute->getEntityType());
    }
}
