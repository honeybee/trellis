<?php

namespace Trellis\Tests\EntityType\Attribute\Boolean;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Boolean\Boolean;
use Trellis\EntityType\Attribute\Boolean\BooleanAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Entity\EntityInterface;
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

    /**
     * @dataProvider provideReservedAttributeNames
     * @expectedException \Trellis\Exception
     */
    public function testConstructWithReservedName($reserved_attribute_name)
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        new BooleanAttribute($reserved_attribute_name, $entity_type);
    } // @codeCoverageIgnore

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $bool_attribute = new BooleanAttribute('my_bool', $entity_type);

        $this->assertInstanceOf(Boolean::CLASS, $bool_attribute->createValue($entity));
        $this->assertInstanceOf(Boolean::CLASS, $bool_attribute->createValue($entity, new Boolean(true)));
        $this->assertInstanceOf(Boolean::CLASS, $bool_attribute->createValue($entity, true));
    }

    /**
     * @codeCoverageIgnore
     */
    public function provideReservedAttributeNames()
    {
        return [
            [ 'entity_type' ],
            [ 'entity_parent' ],
            [ 'entity_root' ]
        ];
    }
}
