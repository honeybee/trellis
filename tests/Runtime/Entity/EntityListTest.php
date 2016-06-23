<?php

namespace Trellis\Tests\Runtime\Entity;

use Trellis\Runtime\Entity\EntityList;
use Trellis\Tests\Runtime\Fixtures\ArticleType;
use Trellis\Tests\Runtime\Fixtures\CategoryType;
use Trellis\Tests\TestCase;

class EntityListTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = new EntityList;

        $this->assertInstanceOf(EntityList::CLASS, $collection);
    }

    public function testAddEntityToEmptyCollection()
    {
        $type = new ArticleType;
        $collection = new EntityList;

        $test_entity = $type->createEntity();
        $collection->addItem($test_entity);

        $first_entity = $collection->getFirst();
        $this->assertEquals($test_entity, $first_entity);
    }

    public function testAddEntityToNonEmptyCollection()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity();

        $collection = new EntityList([ $test_entity ]);

        $collection->addItem($test_entity);

        $first_entity = $collection[0];
        $second_entity = $collection[1];

        $this->assertEquals($test_entity, $first_entity);
        $this->assertEquals($test_entity, $second_entity);
    }

    public function testGetEntityByIdenitifier()
    {
        $uuid = '539fb03b-9bc3-47d9-886d-77f56d390d94';
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => $uuid ]);

        $collection = new EntityList([ $test_entity ]);

        $this->assertEquals($test_entity, $collection->getEntityByIdentifier($uuid));
        $this->assertNull($collection->getEntityByIdentifier('notfound'));
    }

    public function testContainsMultipleTypes()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity();

        $collection = new EntityList([ $test_entity ]);
        $this->assertFalse($collection->containsMultipleTypes());

        $type2 = new CategoryType;
        $test_entity2 = $type2->createEntity();

        $collection->addItem($test_entity2);
        $this->assertTrue($collection->containsMultipleTypes());
    }

    public function testCountShouldBeCorrectAfterAddingEntities()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity();

        $list = new EntityList([ ]);

        $list->push($test_entity);
        $list->push($test_entity);

        $entity1 = $list[0];
        $entity2 = $list[1];

        $this->assertEquals($test_entity, $entity1);
        $this->assertEquals($test_entity, $entity2);
        $this->assertCount(2, $list);
        $this->assertTrue(count($list) === 2);
        $this->assertTrue($list->getSize() === 2);
    }
}
