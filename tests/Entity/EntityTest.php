<?php

namespace Trellis\Tests\Entity;

use Trellis\EntityType\Attribute\EntityList\EntityList;
use Trellis\EntityType\Attribute\Text\Text;
use Trellis\EntityType\Attribute\Uuid\Uuid;
use Trellis\Entity\EntityInterface;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;

class EntityTest extends TestCase
{
    public function testConstruct()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity([ 'uuid' => Uuid::generate() ]);

        $this->assertInstanceOf(EntityInterface::CLASS, $article);
    }

    public function testGetParent()
    {
        $article_type = new ArticleType;
        $paragraph_type = $article_type->getAttribute('content_objects')
            ->getEntityTypeMap()
                ->byPrefix('paragraph');
        $kicker_attr = $paragraph_type->getAttribute('kicker');

        $this->assertEquals($article_type, $kicker_attr->getRootEntityType());
        $this->assertEquals($article_type, $kicker_attr->getParent()->getEntityType());
    }

    /**
     * @expectedException \Trellis\Exception
     */
    public function testConstructWithoutIdentifier()
    {
        $article_type = new ArticleType;
        $article_type->createEntity();
    } // @codeCoverageIgnore

    public function testGetValue()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity([
            'title' => 'Hello world!',
            'uuid' => '375ef3c0-db23-481a-8fdb-533ac47fb9f0',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c',
                    'kicker' => 'hey ho!',
                    'content' => 'this is the content!'
                ]
            ]
        ]);
        $paragraph = $article->get('content_objects.0');

        $this->assertTrue($article->has('uuid'));
        $this->assertEquals('375ef3c0-db23-481a-8fdb-533ac47fb9f0', $article->getUuid()->toNative());
        $this->assertTrue($article->has('title'));
        $this->assertEquals('Hello world!', $article->getTitle()->toNative());

        $this->assertTrue($paragraph->has('uuid'));
        $this->assertEquals('25184b68-6c2d-46b4-8745-46a859f7dd9c', $paragraph->getUuid()->toNative());
        $this->assertTrue($paragraph->has('kicker'));
        $this->assertEquals('hey ho!', $paragraph->getKicker()->toNative());
        $this->assertTrue($paragraph->has('content'));
        $this->assertEquals('this is the content!', $paragraph->getContent()->toNative());
    }

    public function testEqualTo()
    {
        $article_type = new ArticleType;
        $article_one = $article_type->createEntity([
            'title' => 'Hello world!',
            'uuid' => '375ef3c0-db23-481a-8fdb-533ac47fb9f0',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c',
                    'kicker' => 'hey ho!',
                    'content' => 'this is the content!'
                ]
            ]
        ]);
        $article_two = $article_type->createEntity([
            'title' => 'Hello world!',
            'uuid' => '375ef3c0-db23-481a-8fdb-533ac47fb9f0'
        ]);

        // considered same, due to identifier
        $this->assertTrue($article_one->isEqualTo($article_two));
    }

    public function testJsonSerialize()
    {
        $expected_data = [
            '@type' => 'article',
            'uuid' => '375ef3c0-db23-481a-8fdb-533ac47fb9f0',
            'title' => 'Hello world!',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c',
                    'kicker' => 'hey ho!',
                    'content' => 'this is the content!'
                ]
            ]
        ];

        $article_type = new ArticleType;
        $article = $article_type->createEntity([
            'title' => 'Hello world!',
            'uuid' => '375ef3c0-db23-481a-8fdb-533ac47fb9f0',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c',
                    'kicker' => 'hey ho!',
                    'content' => 'this is the content!'
                ]
            ]
        ]);

        $this->assertEquals(json_encode($expected_data), json_encode($article));
    }

    /**
     * @expectedException \Assert\InvalidArgumentException
     */
    public function testInvalidValue()
    {
        $article_type = new ArticleType;
        $article_type->createEntity([ 'uuid' => Uuid::generate(), 'title' => 23 ]);
    } // @codeCoverageIgnore

    public function testGetEntityList()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity([
            'title' => new Text('Hello world!'),
            'uuid' => Uuid::generate(),
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'uuid' => Uuid::generate(),
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
            'uuid' => '375ef3c0-db23-481a-8fdb-533ac47fb9f0',
            'title' => 'Hello world!',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'uuid' => '25184b68-6c2d-46b4-8745-46a859f7dd9c',
                    'kicker' => 'hey ho!',
                    'content' => 'this is the content!'
                ]
            ]
        ];

        $article_type = new ArticleType;
        $article = $article_type->createEntity([
            'title' => 'Hello world!',
            'uuid' => '375ef3c0-db23-481a-8fdb-533ac47fb9f0',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'uuid' => new Uuid('25184b68-6c2d-46b4-8745-46a859f7dd9c'),
                    'kicker' => new Text('hey ho!'),
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
            'uuid' => Uuid::generate(),
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'uuid' => Uuid::generate(),
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
            'uuid' => Uuid::generate(),
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'uuid' => Uuid::generate(),
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
        $article = $article_type->createEntity([ 'uuid' => Uuid::generate() ]);
        $article->has('foobar');
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Exception
     */
    public function testInvalidPath()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity([ 'uuid' => Uuid::generate() ]);
        $article->get([ 'content_objects.0', 'foo.0' ]);
    } // @codeCoverageIgnore
}
