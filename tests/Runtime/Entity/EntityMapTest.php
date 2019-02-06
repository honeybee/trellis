<?php

namespace Trellis\Tests\Runtime\Entity;

use Trellis\Runtime\Entity\EntityMap;
use Trellis\Tests\Runtime\Fixtures\ArticleType;
use Trellis\Tests\TestCase;

class EntityMapTest extends TestCase
{
    public function testCreateEmptyMap()
    {
        $map = new EntityMap;

        $this->assertInstanceOf(EntityMap::CLASS, $map);
        $this->assertCount(0, $map);
    }

    public function testCreateMap()
    {
        $type = new ArticleType;
        $map = new EntityMap([
           $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]),
           $type->createEntity([ 'uuid' => 'd74d3f93-ceba-4782-95ae-92458b4df34c' ])
        ]);

        $this->assertInstanceOf(EntityMap::CLASS, $map);
        $this->assertCount(2, $map);
    }

    /**
     * @expectedException Trellis\Common\Error\InvalidTypeException
     */
    public function testCreateMapInvalidType()
    {
        $map = new EntityMap([ new \stdClass ]);
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     * @expectedExceptionMessage Item already exists
     */
    public function testCreateMapDuplicateIdentifiers()
    {
        $type = new ArticleType;
        $map = new EntityMap([
           $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]),
           $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ])
        ]);
    }

    public function testAddEntityToEmptyMap()
    {
        $type = new ArticleType;
        $map = new EntityMap;

        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map->setItem($test_entity->getIdentifier(), $test_entity);

        $this->assertEquals($test_entity, $map->getItem($test_entity->getIdentifier()));
    }

    public function testHasKeySucceeds()
    {
        $type = new ArticleType;
        $map = new EntityMap;

        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map->setItem($test_entity->getIdentifier(), $test_entity);

        $this->assertTrue($map->hasKey($test_entity->getIdentifier()));
    }

    public function testHasItemSucceeds()
    {
        $type = new ArticleType;
        $map = new EntityMap;

        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map->setItem($test_entity->getIdentifier(), $test_entity);

        $this->assertTrue($map->hasItem($test_entity));
    }

    public function testHasKeySucceedsWhenConstructedWithItems()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map = new EntityMap([$test_entity]);
        $this->assertTrue($map->hasKey($test_entity->getIdentifier()));
    }

    public function testHasItemSucceedsWhenConstructedWithItems()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map = new EntityMap([$test_entity]);
        $this->assertTrue($map->hasItem($test_entity));
    }

    public function testHasKeySucceedsWhenConstructedWithTwoDifferentItemsOfSameIdentifier()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $other_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map = new EntityMap([$test_entity]);
        $this->assertSame($test_entity->getIdentifier(), $other_entity->getIdentifier());
        $this->assertTrue($map->hasKey($other_entity->getIdentifier()));
    }

    public function testSetItemsWorksForEntityMaps()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $other_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map = new EntityMap([$test_entity]);
        $new_map = new EntityMap();
        $new_map->setItems($map->getItems());
        $this->assertSame($map->getKeys(), $new_map->getKeys(), 'both maps should have the same identifiers as keys');
    }

    public function testSetItemsWorksForEntityMapsWhenOnlyEntitiesAreGiven()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $other_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map = new EntityMap([$test_entity]);
        $new_map = new EntityMap();
        $new_map->setItems($map->getValues());
        $this->assertSame($map->getKeys(), $new_map->getKeys(), 'both maps should have the same identifiers as keys');
    }

    public function testHasItemSucceedsWhenConstructedWithTwoDifferentItemsOfSameIdentifier()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map = new EntityMap([$test_entity]);
        $other_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $this->assertSame($test_entity->getIdentifier(), $other_entity->getIdentifier());
        $this->assertTrue($map->hasItem($other_entity));
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     * @expectedExceptionMessage Item already exists
     */
    public function testAddExistingEntityToMapThatContainsEntityWithTheSameIdentifier()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map = new EntityMap([ $test_entity ]);
        $other_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map->setItem($other_entity->getIdentifier(), $other_entity);
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     * @expectedExceptionMessage Item already exists
     */
    public function testAppendEntityMapToMapThatAlreadyContainsAnEntityWithTheSameIdentifierThrows()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $other_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);
        $map = new EntityMap([ $test_entity ]);
        $other_map = new EntityMap([ $other_entity ]);
        $map->append($other_map);
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     * @expectedExceptionMessage Item already exists
     */
    public function testAddExistingEntityToMap()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);

        $map = new EntityMap([ $test_entity ]);
        $map->setItem($test_entity->getIdentifier(), $test_entity);
    }

    public function testRemoveNonexistentEntity()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);

        $map = new EntityMap;

        $this->assertNull($map->removeItem($test_entity));
    }

    public function testRemoveExistingEntity()
    {
        $type = new ArticleType;
        $test_entity = $type->createEntity([ 'uuid' => '2d10d19a-7aca-4d87-aa34-1ea9a5604138' ]);

        $map = new EntityMap([ $test_entity ]);

        $this->assertNull($map->removeItem($test_entity));
    }
}
