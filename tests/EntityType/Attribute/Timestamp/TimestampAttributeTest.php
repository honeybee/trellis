<?php

namespace Trellis\Tests\EntityType\Attribute\Timestamp;

use DateTimeImmutable;
use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Timestamp\Timestamp;
use Trellis\EntityType\Attribute\Timestamp\TimestampAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Entity\EntityInterface;
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

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $timestamp_attribute = new TimestampAttribute('my_timestamp', $entity_type);

        $this->assertInstanceOf(Timestamp::CLASS, $timestamp_attribute->createValue($entity));
        $this->assertInstanceOf(
            Timestamp::CLASS,
            $timestamp_attribute->createValue($entity, new Timestamp(new DateTimeImmutable))
        );
        $this->assertInstanceOf(
            Timestamp::CLASS,
            $timestamp_attribute->createValue($entity, '2016-07-04T19:27:07.000000+02:00')
        );
    }
}
