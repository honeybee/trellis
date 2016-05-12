<?php

namespace Trellis\Tests\Runtime\Entity;

use Trellis\Common\Collection\ArrayList;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Entity\EntityList;
use Trellis\Tests\Runtime\Entity\Fixtures\EntityTestProxy;
use Trellis\Tests\Runtime\Fixtures\ArticleType;
use Trellis\Tests\Runtime\Fixtures\Paragraph;
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

    /**
     * @dataProvider provideArticleList
     */
    public function testWithUpdatedEntitiesWithPositiveFilter(EntityList $article_list)
    {
        $new_content = 'Updated content, is expected on paragraph only.';
        $updated_list = $article_list->withUpdatedEntities(
            [ 'content' => $new_content ],
            function (EntityInterface $entity) {
                return $entity instanceof Paragraph && $entity->getTitle() === 'Pargraph says hello too!';
            }
        );

        $article = $updated_list->getFirst();
        $paragraph = $article->getContentObjects()->getFirst();

        $this->assertInstanceOf(EntityList::CLASS, $updated_list);
        $this->assertEquals($article->getContent(), 'Initial article content ...');
        $this->assertEquals($paragraph->getContent(), $new_content);
    }

    /**
     * @dataProvider provideArticleList
     */
    public function testWithUpdatedEntitiesWithNegativeFilter(EntityList $article_list)
    {
        $updated_list = $article_list->withUpdatedEntities(
            [ 'content' =>  'This must not be reflected within the copied list.' ],
            function (EntityInterface $entity) {
                return false;
            }
        );

        $article = $updated_list->getFirst();
        $paragraph = $article->getContentObjects()->getFirst();

        $this->assertInstanceOf(EntityList::CLASS, $updated_list);
        $this->assertEquals($article->getContent(), 'Initial article content ...');
        $this->assertEquals($paragraph->getContent(), 'Some arbitary initial paragraph content ...');
    }

    /**
     * @dataProvider provideArticleList
     */
    public function testWithUpdatedEntitiesWithWithOutFilter(EntityList $article_list)
    {
        $new_content = 'This must be reflected within the copied and updated paragraph (content_object list).';
        $updated_list = $article_list->withUpdatedEntities([ 'content' => $new_content ]);

        $article = $updated_list->getFirst();
        $paragraph = $article->getContentObjects()->getFirst();

        $this->assertInstanceOf(EntityList::CLASS, $updated_list);
        $this->assertEquals($article->getContent(), $new_content);
        $this->assertEquals($paragraph->getContent(), $new_content);
    }

    public function provideArticleList()
    {
        $article_type = new ArticleType();

        $article_list = new EntityList([
            $article_type->createEntity([
                'headline' => 'Hello World!',
                'content' => 'Initial article content ...',
                'content_objects' => [
                    [
                        '@type' => 'paragraph',
                        'title' => 'Pargraph says hello too!',
                        'content' => 'Some arbitary initial paragraph content ...'
                    ]
                ]
            ])
        ]);

        return [
            [ $article_list ]
        ];
    }
}
