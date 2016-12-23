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
        $article = $article_type->makeEntity([ 'id' => 23, 'title' => 'Foobar' ]);
        /* @var ParagraphType $paragraph_type */
        $paragraph_type = $article_type->getAttribute('content_objects')->getEntityTypeMap()->get('paragraph');
        $paragraph = $paragraph_type->makeEntity([ 'kicker' => 'hey' ], $article);
        $paragraph2 = $paragraph->withValue('kicker', 'ho');
        $entity_list1 = new EntityList([ $paragraph, $paragraph ]);
        $entity_list2 = new EntityList([ $paragraph, $paragraph2 ]);
        $this->assertCount(2, $entity_list1);
        $this->assertFalse($entity_list1->equals($entity_list2));
        $this->assertTrue($entity_list1->equals($entity_list1));
    }

    public function testDiff()
    {
        $article_type = new ArticleType;
        $article = $article_type->makeEntity([ 'id' => 23, 'title' => 'Foobar' ]);
        /* @var ParagraphType $paragraph_type */
        $paragraph_type = $article_type->getAttribute('content_objects')->getEntityTypeMap()->get('paragraph');
        $paragraph = $paragraph_type->makeEntity([ 'kicker' => 'hey' ], $article);
        $paragraph2 = $paragraph->withValue('kicker', 'ho');
        $entity_list1 = new EntityList([ $paragraph, $paragraph ]);
        $entity_list2 = new EntityList([ $paragraph, $paragraph2 ]);
        $this->assertCount(1, $entity_list1->diff($entity_list2));
    }
}
