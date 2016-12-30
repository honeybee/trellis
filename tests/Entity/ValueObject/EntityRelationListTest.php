<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\EntityRelationList;
use Trellis\Entity\ValueObject\Text;
use Trellis\EntityType\Attribute\EntityRelationListAttribute;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\CategoryRelation;
use Trellis\Tests\Fixture\CategoryRelationType;
use Trellis\Tests\TestCase;

final class EntityRelationListTest extends TestCase
{
    private const FIXED_DATA = [ [
        "id" => 42,
        "related_id" => 5,
        "name" => "Sports"
    ], [
        "id" => 7,
        "related_id" => 3,
        "name" => "Politics"
    ] ];

    /**
     * @var CategoryRelation $category1
     */
    private $category1;

    /**
     * @var CategoryRelation $category2
     */
    private $category2;

    /**
     * @var EntityRelationList $entity_list
     */
    private $entity_list;

    public function testToNative(): void
    {
        $expected = [ $this->category1->toNative(), $this->category2->toNative() ];
        $this->assertEquals($expected, $this->entity_list->toNative());
    }

    public function testEquals(): void
    {
        $same_list = new EntityRelationList([$this->category1, $this->category2]);
        $this->assertTrue($this->entity_list->equals($same_list));
        $different_list = new EntityRelationList([$this->category1, $this->category1]);
        $this->assertFalse($this->entity_list->equals($different_list));
        $empty_list = new EntityRelationList;
        $this->assertFalse($this->entity_list->equals($empty_list));
        $this->assertTrue($empty_list->equals(new EntityRelationList));
    }

    public function testIsEmpty(): void
    {
        $this->assertTrue((new EntityRelationList)->isEmpty());
        $this->assertFalse($this->entity_list->isEmpty());
    }

    public function testCount(): void
    {
        $this->assertCount(2, $this->entity_list);
        $this->assertCount(0, new EntityRelationList);
    }

    public function testGetIterator()
    {
        $this->assertEquals(2, iterator_count($this->entity_list));
    }

    public function testGetFirst(): void
    {
        $this->assertEquals($this->category1, $this->entity_list->getFirst());
    }

    public function testAdd(): void
    {
        $this->assertCount(2, (new EntityRelationList)->add($this->category1)->add($this->category2));
    }

    public function testRemove(): void
    {
        $this->assertCount(1, $this->entity_list->remove($this->category1));
    }

    public function testOffsetGet(): void
    {
        $this->assertEquals($this->category1, $this->entity_list[0]);
        $this->assertEquals($this->category2, $this->entity_list[1]);
    }

    public function testGetPos(): void
    {
        $this->assertEquals(0, $this->entity_list->getPos($this->category1));
        $this->assertEquals(1, $this->entity_list->getPos($this->category2));
    }

    public function testOffsetExists(): void
    {
        $this->assertTrue(isset($this->entity_list[0]));
        $this->assertTrue(isset($this->entity_list[1]));
        $this->assertFalse(isset($this->entity_list[2]));
    }

    /**
     * @expectedException \Trellis\Error\MutabilityError
     */
    public function testOffsetSetNotAllowed(): void
    {
        $this->entity_list[1] = $this->category1;
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\MutabilityError
     */
    public function testOffsetUnsetNotAllowed(): void
    {
        unset($this->entity_list[1]);
    } // @codeCoverageIgnore

    public function testGetLast(): void
    {
        $this->assertEquals($this->category2, $this->entity_list->getLast());
    }

    public function testDiff(): void
    {
        $this->assertCount(1, $this->entity_list->diff(new EntityRelationList([ $this->category1 ])));
    }

    public function testToString(): void
    {
        $this->assertEquals("CategoryRelation:42,\nCategoryRelation:7", (string)$this->entity_list);
    }

    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testIncompatibleTypeComparison(): void
    {
        $this->entity_list->equals(new Text("wont work"));
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        $article = (new ArticleType)->makeEntity();
        /* @var EntityRelationListAttribute $categories_attr */
        $categories_attr = $article->getEntityType()->getAttribute("categories");
        /* @var CategoryRelationType $category_rel_type */
        $category_rel_type = $categories_attr->getEntityTypeMap()->get("category_relation");
        $this->category1 = $category_rel_type->makeEntity(self::FIXED_DATA[0], $article);
        $this->category2 = $category_rel_type->makeEntity(self::FIXED_DATA[1], $article);
        $this->entity_list = new EntityRelationList([ $this->category1, $this->category2 ]);
    }
}
