<?php

namespace Trellis\Tests\Entity;

use Trellis\Entity\ValueObject\EntityList;
use Trellis\Entity\ValueObjectMap;
use Trellis\Entity\ValueObject\Integer;
use Trellis\Entity\ValueObject\Text;
use Trellis\EntityType\Attribute\EntityListAttribute;
use Trellis\Tests\Fixture\Article;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\Paragraph;
use Trellis\Tests\TestCase;

class EntityTest extends TestCase
{
    public function testGetParent(): void
    {
        $article_type = new ArticleType;
        /* @var EntityListAttribute $content_objects */
        $content_objects = $article_type->getAttribute('content_objects');
        $kicker_attr = $content_objects->getEntityTypeMap()->get('paragraph')->getAttribute('kicker');
        $this->assertEquals($article_type, $kicker_attr->getParent()->getEntityType());
    }

    public function testGetValue(): void
    {
        /* @var Article $article */
        $article = (new ArticleType)->makeEntity([
            'title' => 'Hello world!',
            'id' => 23,
            'content_objects' => [ [
                '@type' => 'paragraph',
                'id' => 42,
                'kicker' => 'hey ho!',
                'content' => 'this is the content!'
            ] ]
        ]);
        $this->assertTrue($article->has('id'));
        $this->assertEquals(23, $article->getIdentity()->toNative());
        $this->assertTrue($article->has('title'));
        $this->assertEquals('Hello world!', $article->getTitle()->toNative());
        /* @var Paragraph $paragraph */
        $paragraph = $article->get('content_objects.0');
        $this->assertTrue($paragraph->has('id'));
        $this->assertEquals(42, $paragraph->getIdentity()->toNative());
        $this->assertTrue($paragraph->has('kicker'));
        $this->assertEquals('hey ho!', $paragraph->getKicker()->toNative());
        $this->assertTrue($paragraph->has('content'));
        $this->assertEquals('this is the content!', $paragraph->getContent()->toNative());
    }

    public function testWithValue(): void
    {
        /* @var Article $article */
        $article_type = new ArticleType;
        $article = $article_type->makeEntity([
            'title' => 'Hello world!',
            'id' => 23
        ]);
        /* @var Article $new_article */
        $new_article = $article->withValue('content_objects', [ [
            '@type' => 'paragraph',
            'id' => 42,
            'kicker' => 'hey ho!',
            'content' => 'this is the content!'
        ]]);
        $this->assertFalse($article === $new_article);
        $this->assertCount(0, $article->getContentObjects());
        $this->assertCount(1, $new_article->getContentObjects());
    }

    public function testDiff(): void
    {
        $article = (new ArticleType)->makeEntity([
            'title' => 'Hello world!',
            'id' => 23
        ]);
        $diff_data = [
            'title' => 'This is different',
            'content_objects' => [ [
                '@type' => 'paragraph',
                'id' => 42,
                'kicker' => 'hey ho!',
                'content' => 'this is the content!'
            ] ]
        ];
        $new_article = $article->withValues($diff_data);
        $diff = $new_article->getValueObjectMap()->diff($article->getValueObjectMap());
        $this->assertInstanceOf(ValueObjectMap::CLASS, $diff);
        $this->assertEquals($diff_data, $diff->toNative());
    }

    public function testIsSameAs(): void
    {
        $article_type = new ArticleType;
        $article_one = $article_type->makeEntity([
            'title' => 'Hello world!',
            'id' => 23,
            'content_objects' => [ [
                '@type' => 'paragraph',
                'id' => 42,
                'kicker' => 'hey ho!',
                'content' => 'this is the content!'
            ] ]
        ]);
        $article_two = $article_type->makeEntity([
            'title' => 'Hello world!',
            'id' => 23
        ]);
        // considered same, due to identifier
        $this->assertTrue($article_one->isSameAs($article_two));
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testInvalidValue(): void
    {
        (new ArticleType)->makeEntity([ 'id' => 23, 'title' =>  [ 123 ] ]);
    } // @codeCoverageIgnore

    public function testGetEntityList(): void
    {
        /* @var Article $article */
        $article = (new ArticleType)->makeEntity([
            'title' => new Text('Hello world!'),
            'id' => 23,
            'content_objects' => [ [
                '@type' => 'paragraph',
                'id' => 42,
                'kicker' => 'hey ho!',
                'content' => 'this is the content!'
            ] ]
        ]);
        $this->assertInstanceOf(EntityList::CLASS, $article->getContentObjects());
        $this->assertEquals('hey ho!', $article->get('content_objects.0-kicker')->toNative());
    }

    public function testToNative(): void
    {
        $article = (new ArticleType)->makeEntity([
            'title' => 'Hello world!',
            'id' => 23,
            'content_objects' => [ [
                '@type' => 'paragraph',
                'id' => new Integer(42),
                'kicker' => new Text('hey ho!'),
                'content' => 'this is the content!'
            ] ]
        ]);
        $this->assertEquals([
            '@type' => 'article',
            'id' => 23,
            'title' => 'Hello world!',
            'content_objects' => [ [
                '@type' => 'paragraph',
                'id' => 42,
                'kicker' => 'hey ho!',
                'content' => 'this is the content!'
            ] ]
        ], $article->toNative());
    }

    public function testRoot(): void
    {
        /* @var Article $article */
        $article_type = new ArticleType;
        $article = $article_type->makeEntity([
            'title' => 'Hello world!',
            'id' => 23,
            'content_objects' => [ [
                '@type' => 'paragraph',
                'id' => 42,
                'kicker' => 'hey ho!',
                'content' => 'this is the content!'
            ] ]
        ]);
        /* @var Paragraph $paragraph */
        $paragraph = $article->getContentObjects()->getFirst();
        $this->assertTrue($article === $paragraph->getEntityRoot());
        $this->assertTrue($article_type === $paragraph->getEntityRoot()->getEntityType());
    }

    public function testToValuePath(): void
    {
        /* @var Article $article */
        $article = (new ArticleType)->makeEntity([
            'title' => 'Hello world!',
            'id' => 23,
            'content_objects' => [ [
                '@type' => 'paragraph',
                'id' => 42,
                'kicker' => 'hey ho!',
                'content' => 'this is the content!'
            ] ]
        ]);
        /* @var Paragraph $paragraph */
        $paragraph = $article->getContentObjects()->getFirst();
        $this->assertEquals('content_objects.0', $paragraph->toPath());
    }

    /**
     * @expectedException \Trellis\Error\UnknownAttribute
     */
    public function testInvalidHas(): void
    {
        $article = (new ArticleType)->makeEntity([ 'id' => 23 ]);
        $article->has('foobar');
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\UnknownAttribute
     */
    public function testInvalidPath(): void
    {
        $article = (new ArticleType)->makeEntity([ 'id' => 23 ]);
        $article->get('foo.0');
    } // @codeCoverageIgnore
}
