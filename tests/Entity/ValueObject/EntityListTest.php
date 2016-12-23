<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\EntityList;
use Trellis\EntityType\Attribute\EntityListAttribute;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\ParagraphType;
use Trellis\Tests\TestCase;

class EntityListTest extends TestCase
{
    public function testCreation(): void
    {
        $article_type = new ArticleType;
        $article = $article_type->makeEntity([ 'id' => 23, 'title' => 'Foobar' ]);
        /* @var EntityListAttribute $content_objects */
        $content_objects = $article_type->getAttribute('content_objects');
        /* @var ParagraphType $paragraph_type */
        $paragraph_type = $content_objects->getEntityTypeMap()->get('paragraph');
        $paragraph = $paragraph_type->makeEntity([ 'kicker' => 'hey' ], $article);
        $paragraph2 = $paragraph->withValue('kicker', 'ho');
        $entity_list1 = new EntityList([ $paragraph, $paragraph ]);
        $entity_list2 = new EntityList([ $paragraph, $paragraph2 ]);
        $this->assertCount(2, $entity_list1);
        $this->assertFalse($entity_list1->equals($entity_list2));
        $this->assertTrue($entity_list1->equals($entity_list1));
    }

    public function testDiff(): void
    {
        $article_type = new ArticleType;
        $article = $article_type->makeEntity([ 'id' => 23, 'title' => 'Foobar' ]);
        /* @var EntityListAttribute $content_objects */
        $content_objects = $article_type->getAttribute('content_objects');
        /* @var ParagraphType $paragraph_type */
        $paragraph_type = $content_objects->getEntityTypeMap()->get('paragraph');
        $paragraph = $paragraph_type->makeEntity([ 'kicker' => 'hey' ], $article);
        $paragraph2 = $paragraph->withValue('kicker', 'ho');
        $entity_list1 = new EntityList([ $paragraph, $paragraph ]);
        $entity_list2 = new EntityList([ $paragraph, $paragraph2 ]);
        $this->assertCount(1, $entity_list1->diff($entity_list2));
    }
}
