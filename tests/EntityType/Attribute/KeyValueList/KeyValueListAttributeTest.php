<?php

namespace Trellis\Tests\EntityType\Attribute\KeyValueList;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\KeyValueList\KeyValueList;
use Trellis\EntityType\Attribute\KeyValueList\KeyValueListAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Entity\EntityInterface;
use Trellis\Tests\TestCase;

class KeyValueListAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $kv_list = new KeyValueListAttribute('my_key_value_list', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $kv_list);
        $this->assertEquals('my_key_value_list', $kv_list->getName());
        $this->assertEquals($entity_type, $kv_list->getEntityType());
    }

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $kv_list = new KeyValueListAttribute('my_key_value_list', $entity_type);

        $this->assertInstanceOf(KeyValueList::CLASS, $kv_list->createValue($entity));
        $this->assertInstanceOf(
            KeyValueList::CLASS,
            $kv_list->createValue($entity, new KeyValueList([ 'message' => 'hello world!' ]))
        );
        $this->assertInstanceOf(KeyValueList::CLASS, $kv_list->createValue($entity, [ 'message' => 'hello world!' ]));
    }
}
