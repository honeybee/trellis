<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\EntityList;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\ParagraphType;
use Trellis\Tests\TestCase;

class EntityListTest extends TestCase
{
    public function testCreation()
    {
        $article_type = new ArticleType;
        $article1 = $article_type->makeEntity([
            'id' => 23,
            'title' => 'Hello world!'
        ]);
        $article2 = $article1->withValue('title', 'Foobar');
        $entity_list1 = new EntityList([ $article1, $article1 ]);
        $entity_list2 = new EntityList([ $article1, $article2 ]);
        $this->assertCount(2, $entity_list1);
        $this->assertFalse($entity_list1->equals($entity_list2));
        $this->assertTrue($entity_list1->equals($entity_list1));
    }

    public function testDiff()
    {
        $article_type = new ArticleType;
        $article1 = $article_type->makeEntity([
            'id' => 23,
            'title' => 'Hello world!'
        ]);
        $article2 = $article1->withValue('title', 'Foobar');
        $entity_list1 = new EntityList([ $article1, $article1 ]);
        $entity_list2 = new EntityList([ $article1, $article2 ]);
        $this->assertCount(1, $entity_list1->diff($entity_list2));
    }
}
