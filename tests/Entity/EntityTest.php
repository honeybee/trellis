<?php

namespace Trellis\Tests\Entity;

use Trellis\EntityType\Attribute\EntityList\EntityList;
use Trellis\Entity\EntityInterface;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;

class EntityTest extends TestCase
{
    public function testConstruct()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity();

        $this->assertInstanceOf(EntityInterface::CLASS, $article);
    }

    public function testGetValue()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity([ 'title' => 'Hello world!' ]);

        $this->assertFalse($article->has('uuid'));
        $this->assertTrue($article->has('title'));
        $this->assertEquals('Hello world!', $article->getTitle()->toNative());
    }

    /**
     * @expectedException Assert\InvalidArgumentException
     */
    public function testInvalidValue()
    {
        $article_type = new ArticleType;
        $article_type->createEntity([ 'title' => 23 ]);
    } // @codeCoverageIgnore

    public function testGetEntityList()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity([
            'title' => 'Hello world!',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'kicker' => 'hey ho!',
                    'content' => 'this is the content!'
                ]
            ]
        ]);

        $this->assertInstanceOf(EntityList::CLASS, $article->getContentObjects());
        $this->assertEquals('hey ho!', $article->get('content_objects.0-kicker')->toNative());
    }

    public function testToArray()
    {
        $expected_data = [
            '@type' => 'article',
            'uuid' => null,
            'title' => 'Hello world!',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'uuid' => null,
                    'kicker' => 'hey ho!',
                    'content' => 'this is the content!'
                ]
            ]
        ];

        $article_type = new ArticleType;
        $article = $article_type->createEntity([
            'title' => 'Hello world!',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'kicker' => 'hey ho!',
                    'content' => 'this is the content!'
                ]
            ]
        ]);
        $this->assertEquals($expected_data, $article->toArray());
    }

    public function testRoot()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity([
            'title' => 'Hello world!',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'kicker' => 'hey ho!',
                    'content' => 'this is the content!'
                ]
            ]
        ]);
        $paragraph = $article->getContentObjects()->getFirst();
        $this->assertEquals($article, $paragraph->root());
        $this->assertEquals($article_type, $paragraph->root()->type());
    }

    public function testToValuePath()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity([
            'title' => 'Hello world!',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'kicker' => 'hey ho!',
                    'content' => 'this is the content!'
                ]
            ]
        ]);
        $paragraph = $article->getContentObjects()->getFirst();

        $this->assertEquals('content_objects.0', $paragraph->toValuePath());
    }

    /**
     * @expectedException \Trellis\Exception
     */
    public function testInvalidHas()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity();
        $article->has('foobar');
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Exception
     */
    public function testInvalidPath()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity();
        $article->get([ 'content_objects.0', 'foo.0' ]);
    } // @codeCoverageIgnore
}
