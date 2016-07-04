<?php

namespace Trellis\Tests\EntityType\Attribute\Timestamp;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Timestamp\TimestampAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Tests\TestCase;

class TimestampAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $timestamp_attribute = new TimestampAttribute('my_timestamp', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $timestamp_attribute);
        $this->assertEquals('my_timestamp', $timestamp_attribute->getName());
        $this->assertEquals($entity_type, $timestamp_attribute->getEntityType());
    }
}
