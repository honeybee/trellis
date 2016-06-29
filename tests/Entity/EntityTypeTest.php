<?php

namespace Trellis\Tests\Entity;

use Trellis\Attribute\AttributeMapInterface;
use Trellis\Attribute\TextAttribute;
use Trellis\Attribute\UuidAttribute;
use Trellis\Entity\EntityType;
use Trellis\Entity\EntityTypeInterface;
use Trellis\Tests\Fixtures\ArticleType;
use Trellis\Tests\TestCase;

class EntityTypeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = new ArticleType();

        $this->assertInstanceOf(EntityTypeInterface::CLASS, $entity_type);
        $this->assertEquals('Article', $entity_type->getName());
        $this->assertEquals('article', $entity_type->getPrefix());
    }

    public function testGetAttribute()
    {
        $entity_type = new ArticleType();

        $this->assertInstanceOf(TextAttribute::CLASS, $entity_type->getAttribute('title'));
        $this->assertInstanceOf(UuidAttribute::CLASS, $entity_type->getAttribute('uuid'));
    }

    public function testGetAttributes()
    {
        $entity_type = new ArticleType();

        $this->assertInstanceOf(AttributeMapInterface::CLASS, $entity_type->getAttributes());
        $this->assertCount(2, $entity_type->getAttributes());
    }
}
