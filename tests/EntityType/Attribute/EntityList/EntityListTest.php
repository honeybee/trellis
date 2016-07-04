<?php

namespace Trellis\Tests\EntityType\Attribute\EntityList;

use Trellis\EntityType\Attribute\EntityList\EntityList;
use Trellis\Entity\EntityInterface;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\TestCase;

class EntityListTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new EntityList);
    }

    public function testToNative()
    {
        $mock_entities = [];
        $expected_data = [
            [ 'title' => 'bar-number-one', 'msg' => 'hello world from index 0' ],
            [ 'title' => 'bar-number-two', 'msg' => 'hello world from index 1' ],
            [ 'title' => 'bar-number-three', 'msg' => 'hello world from index 2' ],
            [ 'title' => 'bar-number-four', 'msg' => 'hello world from index 3' ]
        ];
        for ($i = 0; $i < 4; $i++) {
            $entity = $this->getMockBuilder(EntityInterface::class, [ 'toArray' ])->getMock();
            $entity->method('toArray')->will($this->returnValue($expected_data[$i]));
            $mock_entities[] = $entity;
        }

        $entity_list = new EntityList;
        $this->assertEquals([], $entity_list->toNative());

        $entity_list = new EntityList($mock_entities);
        $this->assertEquals($expected_data, $entity_list->toNative());
    }

    public function testIsEmpty()
    {
        $entity_list = new EntityList;
        $this->assertTrue($entity_list->isEmpty());
        $entity_list = new EntityList([]);
        $this->assertTrue($entity_list->isEmpty());
    }
}
