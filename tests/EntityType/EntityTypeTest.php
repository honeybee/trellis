<?php

namespace Trellis\Tests\EntityType;

use Trellis\EntityType\Attribute\AttributeMap;
use Trellis\EntityType\Attribute\Text\TextAttribute;
use Trellis\EntityType\Attribute\Uuid\UuidAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Tests\Fixture\ArticleType;
use Trellis\Tests\Fixture\InvalidEntityType;
use Trellis\Tests\TestCase;

class EntityTypeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = new ArticleType;

        $this->assertInstanceOf(EntityTypeInterface::CLASS, $entity_type);
        $this->assertEquals('Article', $entity_type->getName());
        $this->assertEquals('article', $entity_type->getPrefix());
    }

    public function testToTypePath()
    {
        $entity_type = new ArticleType;
        $kicker_attr = $entity_type->getAttribute('content_objects.paragraph-kicker');

        $this->assertEquals('content_objects.paragraph-kicker', $kicker_attr->toTypePath());
    }

    public function testGetAttribute()
    {
        $entity_type = new ArticleType;

        $this->assertInstanceOf(TextAttribute::CLASS, $entity_type->getAttribute('title'));
        $this->assertInstanceOf(UuidAttribute::CLASS, $entity_type->getAttribute('uuid'));
    }

    public function testGetAttributes()
    {
        $entity_type = new ArticleType;

        $this->assertInstanceOf(AttributeMap::CLASS, $entity_type->getAttributes());
        $this->assertCount(3, $entity_type->getAttributes());
        $this->assertCount(2, $entity_type->getAttributes([ 'title', 'content_objects.paragraph-kicker' ]));
    }

    public function testGetParent()
    {
        $entity_type = new ArticleType;
        $paragraph_kicker = $entity_type->getAttribute('content_objects.paragraph-kicker');
        $paragraph_type = $paragraph_kicker->getEntityType();

        $this->assertEquals($entity_type, $paragraph_type->getRoot());
        $this->assertEquals($entity_type, $paragraph_type->getParent());

        $this->assertTrue($paragraph_type->hasParent());
        $this->assertFalse($entity_type->hasParent());

        $this->assertTrue($entity_type->isRootType());
        $this->assertFalse($paragraph_type->isRootType());
    }

    public function testHasAttribute()
    {
        $entity_type = new ArticleType;

        $this->assertTrue($entity_type->hasAttribute('title'));
        $this->assertTrue($entity_type->hasAttribute('content_objects.paragraph-kicker'));
    }

    /**
     * @expectedException \Trellis\Exception
     */
    public function testGetAttributeWithNonExistingAttribute()
    {
        $entity_type = new ArticleType;
        $entity_type->getAttribute('foobar');
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Exception
     */
    public function testInvalidCreateEntity()
    {
        $entity_type = new InvalidEntityType;
        $entity_type->createEntity();
    } // @codeCoverageIgnore
}
