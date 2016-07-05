<?php

namespace Trellis\Tests\EntityType\Attribute\EntityList;

use Trellis\EntityType\Attribute\Boolean\Boolean;
use Trellis\EntityType\Attribute\EntityList\EntityList;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Entity\EntityInterface;
use Trellis\Entity\Value\ValueInterface;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;

class EntityListTest extends TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(ValueInterface::CLASS, new EntityList);
    }

    public function testFromNative()
    {
        $entity_type = new ArticleType;
        $entity = $entity_type->createEntity([ 'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c' ]);
        $entity_list = EntityList::fromNative([
            [
                '@type' => 'paragraph',
                'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c',
                'title' => 'paragraph title'
            ]
        ], $entity_type->getAttribute('content_objects')->getEntityTypeMap(), $entity);

        $this->assertInstanceOf(EntityList::CLASS, $entity_list);
    }

    /**
     * @expectedException \Trellis\Exception
     */
    public function testFromNativeMissingType()
    {
        $entity_type = new ArticleType;
        $entity = $entity_type->createEntity([ 'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c' ]);
        EntityList::fromNative([
            [
                'type' => 'paragraph',
                'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c',
                'title' => 'paragraph title'
            ]
        ], $entity_type->getAttribute('content_objects')->getEntityTypeMap(), $entity);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Exception
     */
    public function testFromNativeInvalidType()
    {
        $entity_type = new ArticleType;
        $entity = $entity_type->createEntity([ 'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c' ]);
        EntityList::fromNative([
            [
                '@type' => 'foobar',
                'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c',
                'title' => 'paragraph title'
            ]
        ], $entity_type->getAttribute('content_objects')->getEntityTypeMap(), $entity);
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Exception
     */
    public function testUnqiueItemConstraintViolation()
    {
        $mock_entities = [];
        $expected_data = [
            [ 'title' => 'bar-number-one', 'msg' => 'hello world from index 0' ],
            [ 'title' => 'bar-number-two', 'msg' => 'hello world from index 1' ]
        ];
        for ($i = 0; $i < count($expected_data); $i++) {
            $entity = $this->getMockBuilder(EntityInterface::class, [ 'toArray' ])->getMock();
            $entity->method('toArray')->will($this->returnValue($expected_data[$i]));
            $mock_entities[] = $entity;
        }
        $mock_entities[] = $mock_entities[0];
        new EntityList($mock_entities);
    } // @codeCoverageIgnore

    public function testToNative()
    {
        $mock_entities = [];
        $expected_data = [
            [ 'title' => 'bar-number-one', 'msg' => 'hello world from index 0' ],
            [ 'title' => 'bar-number-two', 'msg' => 'hello world from index 1' ],
            [ 'title' => 'bar-number-three', 'msg' => 'hello world from index 2' ],
            [ 'title' => 'bar-number-four', 'msg' => 'hello world from index 3' ]
        ];
        for ($i = 0; $i < count($expected_data); $i++) {
            $entity = $this->getMockBuilder(EntityInterface::class, [ 'toArray' ])->getMock();
            $entity->method('toArray')->will($this->returnValue($expected_data[$i]));
            $mock_entities[] = $entity;
        }

        $entity_list = new EntityList;
        $this->assertEquals([], $entity_list->toNative());

        $entity_list = new EntityList($mock_entities);
        $this->assertEquals($expected_data, $entity_list->toNative());
    }

    public function testIsEqualTo()
    {
        $mock_entities = [];
        for ($i = 0; $i < 3; $i++) {
            $entity = $this->getMockBuilder(EntityInterface::class, [ 'toArray' ])->getMock();
            $entity->method('isEqualTo')->will($this->returnValue(true));
            $mock_entities[] = $entity;
        }

        $entity_list = new EntityList($mock_entities);
        $this->assertTrue($entity_list->isEqualTo(new EntityList($mock_entities)));
        $this->assertFalse($entity_list->isEqualTo(new Boolean));

        $entity_list = new EntityList(array_slice($mock_entities, 0, 1));
        $this->assertFalse($entity_list->isEqualTo(new EntityList($mock_entities)));

        $entity = $this->getMockBuilder(EntityInterface::class, [ 'toArray' ])->getMock();
        $entity->method('isEqualTo')->will($this->returnValue(false));
        $mock_entities[] = $entity;
        $this->assertFalse($entity_list->isEqualTo(new EntityList($mock_entities)));
    }

    public function testIsEmpty()
    {
        $entity_list = new EntityList;
        $this->assertTrue($entity_list->isEmpty());
        $entity_list = new EntityList([]);
        $this->assertTrue($entity_list->isEmpty());
    }
}
