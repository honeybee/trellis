<?php

namespace Trellis\Tests\EntityType;

use Trellis\EntityTypeInterface;
use Trellis\Entity\ValueObject\Text;
use Trellis\Tests\Fixture\Article;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\TestCase;
use Trellis\EntityType\AttributeMap;
use Trellis\EntityType\Attribute\IntegerAttribute;
use Trellis\EntityType\Attribute\TextAttribute;

class EntityTypeTest extends TestCase
{
    public function testConstruct(): void
    {
        $entity_type = new ArticleType;
        $this->assertInstanceOf(EntityTypeInterface::CLASS, $entity_type);
        $this->assertEquals('Article', $entity_type->getName());
        $this->assertEquals('article', $entity_type->getPrefix());
    }

    public function testToTypePath(): void
    {
        $entity_type = new ArticleType;
        $kicker_attr = $entity_type->getAttribute('content_objects.paragraph-kicker');
        $this->assertEquals('content_objects.paragraph-kicker', $kicker_attr->toPath());
    }

    public function testGetAttribute(): void
    {
        $entity_type = new ArticleType;
        $this->assertInstanceOf(TextAttribute::CLASS, $entity_type->getAttribute('title'));
        $this->assertInstanceOf(IntegerAttribute::CLASS, $entity_type->getAttribute('id'));
    }

    public function testGetAttributes(): void
    {
        $entity_type = new ArticleType;
        $this->assertInstanceOf(AttributeMap::CLASS, $entity_type->getAttributes());
        $this->assertCount(3, $entity_type->getAttributes());
    }

    public function testGetParent(): void
    {
        $entity_type = new ArticleType;
        $paragraph_kicker = $entity_type->getAttribute('content_objects.paragraph-kicker');
        $paragraph_type = $paragraph_kicker->getEntityType();
        $this->assertEquals($entity_type, $paragraph_type->getRoot());
        $this->assertEquals($entity_type, $paragraph_type->getParent());
        $this->assertTrue($paragraph_type->hasParent());
        $this->assertFalse($entity_type->hasParent());
        $this->assertTrue($entity_type->isRoot());
        $this->assertFalse($paragraph_type->isRoot());
    }

    public function testHasAttribute(): void
    {
        $entity_type = new ArticleType;
        $this->assertTrue($entity_type->hasAttribute('title'));
        $this->assertTrue($entity_type->hasAttribute('content_objects.paragraph-kicker'));
    }

    public function testMakeEntity(): void
    {
        /* @var Article $article */
        $article = (new ArticleType)->makeEntity([
            'title' => 'hello world!',
            'content' => 'this is some test content ...'
        ]);
        $this->assertInstanceOf(Text::CLASS, $article->getTitle());
        $this->assertEquals('hello world!', $article->getTitle()->toNative());
    }

    /**
     * @expectedException \Trellis\Error\InvalidType
     */
    public function testGetAttributeWithNonExistingAttribute(): void
    {
        (new ArticleType)->getAttribute('foobar');
    } // @codeCoverageIgnore
}
