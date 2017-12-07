<?php

namespace Trellis\Tests\Runtime;

use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Attribute\AttributeMap;
use Trellis\Runtime\Attribute\Boolean\BooleanAttribute;
use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Trellis\Runtime\Attribute\GeoPoint\GeoPointAttribute;
use Trellis\Runtime\Attribute\IntegerList\IntegerListAttribute;
use Trellis\Runtime\Attribute\Integer\IntegerAttribute;
use Trellis\Runtime\Attribute\KeyValueList\KeyValueListAttribute;
use Trellis\Runtime\Attribute\TextList\TextListAttribute;
use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\Attribute\Timestamp\TimestampAttribute;
use Trellis\Runtime\Attribute\Url\UrlAttribute;
use Trellis\Runtime\EntityTypeInterface;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Tests\Runtime\Fixtures\ArticleType;
use Trellis\Tests\Runtime\Fixtures\InvalidType;
use Trellis\Tests\Runtime\Fixtures\ParagraphType;
use Trellis\Tests\TestCase;

class EntityTypeTest extends TestCase
{
    public function testCreateArticleType()
    {
        $article_type = new ArticleType();

        $this->assertEquals('Article', $article_type->getName());
        $this->assertEquals(18, $article_type->getAttributes()->getSize());
    }

    public function testAccessNestedParameters()
    {
        $article_type = new ArticleType();

        $this->assertEquals('bar', $article_type->getOption('foo'));
        $this->assertEquals('blub', $article_type->getOption('nested')->get('blah'));
    }

    public function testCreateEmbedType()
    {
        $article_type = new ArticleType();
        $paragraph_type = new ParagraphType($article_type, $article_type->getAttribute('content_objects'));

        $this->assertEquals(3, $paragraph_type->getAttributes()->getSize());
        $this->assertEquals('Paragraph', $paragraph_type->getName());
    }

    public function testGetAttributeMethod()
    {
        $article_type = new ArticleType();

        $this->assertInstanceOf(TextAttribute::CLASS, $article_type->getAttribute('headline'));
        $this->assertInstanceOf(IntegerAttribute::CLASS, $article_type->getAttribute('click_count'));
    }

    public function testGetAttributesMethodPlain()
    {
        $article_type = new ArticleType();
        $attributes = $article_type->getAttributes();

        $this->assertInstanceOf(AttributeMap::CLASS, $attributes);
        $this->assertEquals(18, $attributes->getSize());
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('headline'));
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('content'));
        $this->assertInstanceOf(IntegerAttribute::CLASS, $attributes->getItem('click_count'));
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('author'));
        $this->assertInstanceOf(TimestampAttribute::CLASS, $attributes->getItem('birthday'));
        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('email'));
        $this->assertInstanceOf(UrlAttribute::CLASS, $attributes->getItem('website'));
        $this->assertInstanceOf(TextListAttribute::CLASS, $attributes->getItem('keywords'));
        $this->assertInstanceOf(GeoPointAttribute::CLASS, $attributes->getItem('coords'));
        $this->assertInstanceOf(BooleanAttribute::CLASS, $attributes->getItem('enabled'));
        $this->assertInstanceOf(IntegerListAttribute::CLASS, $attributes->getItem('images'));
        $this->assertInstanceOf(KeyValueListAttribute::CLASS, $attributes->getItem('meta'));
        $this->assertInstanceOf(EmbeddedEntityListAttribute::CLASS, $attributes->getItem('content_objects'));
    }

    public function testGetAttributesMethodFiltered()
    {
        $article_type = new ArticleType();
        $attributes = $article_type->getAttributes([ 'headline', 'click_count' ]);

        $this->assertInstanceOf(AttributeMap::CLASS, $attributes);
        $this->assertEquals(2, $attributes->getSize());

        $this->assertInstanceOf(TextAttribute::CLASS, $attributes->getItem('headline'));
        $this->assertInstanceOf(IntegerAttribute::CLASS, $attributes->getItem('click_count'));
    }

    public function testGetReferencedAttributes()
    {
        $article_type = new ArticleType();
        $referenced_attributes = $article_type->getReferenceAttributes();

        $this->assertEquals(2, $referenced_attributes->getSize());
        $this->assertEquals(
            [
                'categories',
                'categories.referenced_category.subcategories'
            ],
            $referenced_attributes->getKeys()
        );
    }

    public function testCreateEntity()
    {
        $article_type = new ArticleType();
        $entity = $article_type->createEntity();
        $this->assertInstanceOf(EntityInterface::CLASS, $entity);
    }

    public function testCollateAttributes()
    {
        $article_type = new ArticleType();
        $collated_attributes = $article_type->collateAttributes(
            function (AttributeInterface $attribute) {
                return $attribute->getName() === 'content';
            }
        );
        $this->assertInstanceOf(AttributeMap::CLASS, $collated_attributes);
        $this->assertCount(2, $collated_attributes);
    }

    public function testCollateAttributesFlat()
    {
        $article_type = new ArticleType();
        $collated_attributes = $article_type->collateAttributes(
            function (AttributeInterface $attribute) {
                return $attribute->getName() === 'content';
            },
            false
        );
        $this->assertInstanceOf(AttributeMap::CLASS, $collated_attributes);
        $this->assertCount(1, $collated_attributes);
    }

    /**
     * @expectedException Trellis\Common\Error\RuntimeException
     */
    public function testInvalidAttributeException()
    {
        $article_type = new ArticleType();
        $article_type->getAttribute('foobar-attribute-does-not-exist'); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    /**
     * @expectedException Trellis\Common\Error\InvalidTypeException
     */
    public function testInvalidEntityImplementorException()
    {
        $type = new InvalidType();
        $type->createEntity(); // @codeCoverageIgnoreStart
    } // @codeCoverageIgnoreEnd

    public function testGetAttributeByPath()
    {
        $article_type = new ArticleType();
        $attribute = $article_type->getAttribute('content_objects.paragraph.title');

        $this->assertEquals('title', $attribute->getName());
    }
}
