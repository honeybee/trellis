<?php

namespace Trellis\Tests\Runtime\Entity;

use Trellis\Common\Collection\ArrayList;
use Trellis\Runtime\Entity\EntityList;
use Trellis\Tests\Runtime\Entity\Fixtures\EntityTestProxy;
use Trellis\Tests\Runtime\Fixtures\ArticleType;
use Trellis\Tests\TestCase;

class EntityListTest extends TestCase
{
    public function testCreateCollection()
    {
        $collection = new EntityList();

        $this->assertInstanceOf(EntityList::CLASS, $collection);
    }

    public function testAddEntityToEmptyCollection()
    {
        $type = new ArticleType();
        $collection = new EntityList();

        $test_entity = $type->createEntity();
        $collection->addItem($test_entity);

        $first_entity = $collection->getFirst();
        $this->assertEquals($test_entity, $first_entity);
    }

    public function testAddEntityToNonEmptyCollection()
    {
        $type = new ArticleType();
        $test_entity = $type->createEntity();

        $collection = new EntityList([ $test_entity ]);

        $collection->addItem($test_entity);

        $first_entity = $collection[0];
        $second_entity = $collection[1];

        $this->assertEquals($test_entity, $first_entity);
        $this->assertEquals($test_entity, $second_entity);
    }
}
