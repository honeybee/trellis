<?php

namespace Trellis\Tests\Attribute\Uuid;

use Trellis\Attribute\AttributeInterface;
use Trellis\Attribute\Uuid\UuidAttribute;
use Trellis\Entity\EntityTypeInterface;
use Trellis\Tests\TestCase;

class UuidAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $uuid_attribute = new UuidAttribute('my_uuid', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $uuid_attribute);
        $this->assertEquals('my_uuid', $uuid_attribute->getName());
        $this->assertEquals($entity_type, $uuid_attribute->getEntityType());
    }
}
