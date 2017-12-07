<?php

namespace Trellis\Tests\Runtime\Entity;

use Trellis\Common\Collection\ArrayList;
use Trellis\Runtime\Attribute\AttributeValuePath;
use Trellis\Runtime\Attribute\GeoPoint\GeoPoint;
use Trellis\Runtime\Attribute\Image\Image;
use Trellis\Runtime\Attribute\Uuid\UuidAttribute;
use Trellis\Runtime\Entity\EntityChangedEvent;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Entity\EntityList;
use Trellis\Runtime\Entity\EntityMap;
use Trellis\Tests\Runtime\Entity\Fixtures\EntityTestProxy;
use Trellis\Tests\Runtime\Fixtures\ArticleType;
use Trellis\Tests\TestCase;

class EntityTest extends TestCase
{
    public function testCreateEntity()
    {
        $type = new ArticleType;
        $entity = $type->createEntity([
            'headline' => 'hello world!'
        ]);

        $this->assertTrue($entity->isValid());
        $this->assertEquals('hello world!', $entity->getValue('headline'));
    }

    public function testInvalidValue()
    {
        $type = new ArticleType;
        $entity = $type->createEntity([
            'headline' => 'hel' // 'min' = 4, thus invalid
        ]);

        $this->assertFalse($entity->isValid());
        $this->assertEquals(null, $entity->getValue('headline'));
    }

    public function testChangedEvents()
    {
        $type = new ArticleType;
        $entity = $type->createEntity([
            'headline' => 'hel',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'title' => 'this is a paragraph',
                    'text' => 'wat?!'
                ]
            ]
        ]);

        $paragraph = $entity->getValue('content_objects')->getFirst();
        $paragraph->setValue('title', 'Go home embedd-event, you are drunk!');

        $this->assertInstanceOf(EntityChangedEvent::CLASS, $entity->getChanges()->getFirst()->getEmbeddedEvent());
    }

    public function testGetNestedValue()
    {
        $type = new ArticleType;
        $entity = $type->createEntity([
            'headline' => 'hel',
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'title' => 'this is a paragraph',
                    'text' => 'wat?!',
                    'coords' => [ 'lon' => 12.34, 'lat' => 56.78 ]
                ]
            ]
        ]);

        $paragraph = $entity->getValue('content_objects')->getFirst();
        $gp = $paragraph->getValue('coords');

        $this->assertInstanceOf(GeoPoint::CLASS, $gp);
        $this->assertEquals(12.34, $gp->getLongitude());

        $gp = AttributeValuePath::getAttributeValueByPath($entity, 'content_objects.*[0].coords');
        $this->assertInstanceOf(GeoPoint::CLASS, $gp);
        $this->assertEquals(56.78, $gp->getLatitude());
    }

    public function testToNative()
    {
        $data = $this->getExampleValues();
        $type = new ArticleType;
        $entity = $type->createEntity($data);
        $this->assertTrue($entity->isValid(), 'entity should be in valid state');

        $result = $entity->toNative();

        $this->assertEquals(array_keys($data), array_keys($result));
        $this->assertEquals($data['headline'], $result['headline']);
        $this->assertEquals($data['click_count'], $result['click_count']);
        $this->assertEquals($data['email'], $result['email']);
        $this->assertEquals($data['website'], $result['website']);
        $this->assertEquals($data['coords'], $result['coords']);
        $this->assertEquals('2014-12-31T11:45:55.123456+00:00', $result['birthday']); // utc
        $this->assertEquals([ 'some', 'keywords' ], $result['keywords']);
        $this->assertTrue($result['enabled']);
        $this->assertTrue(is_array($result['content_objects']));
        $this->assertTrue(is_array($result['meta']));
        $this->assertTrue(is_array($result['workflow_state']));
    }

    public function testToArray()
    {
        $data = $this->getExampleValues();
        $type = new ArticleType;
        $entity = $type->createEntity($data);
        $this->assertTrue($entity->isValid(), 'entity should be in valid state');

        $result = $entity->toArray();

        $this->assertEquals('article', $result['@type']);
        $this->assertEquals($data['headline'], $result['headline']);
        $this->assertEquals($data['click_count'], $result['click_count']);
        $this->assertEquals($data['email'], $result['email']);
        $this->assertEquals($data['website'], $result['website']);
        $this->assertEquals($data['coords'], $result['coords']);
        $this->assertEquals('2014-12-31T11:45:55.123456+00:00', $result['birthday']); // utc
        $this->assertEquals([ 'some', 'keywords' ], $result['keywords']);
        $this->assertTrue($result['enabled']);
        $this->assertTrue(is_array($result['content_objects']));
        $this->assertTrue(is_array($result['meta']));
        $this->assertTrue(is_array($result['workflow_state']));
    }

    public function testToNativeWithMissingComplexValues()
    {
        $data = $this->getExampleValues();
        unset($data['coords']);

        $type = new ArticleType;
        $entity = $type->createEntity($data);
        $this->assertTrue($entity->isValid(), 'entity should be in valid state');

        $result = $entity->toNative();

        $this->assertEquals(null, $result['coords']);
    }

    public function testToNativeReconstitution()
    {
        $type = new ArticleType;
        $entity = $type->createEntity($this->getExampleValues());

        $this->assertTrue($entity->isValid());

        $result = $entity->toNative();
        $new_entity = $type->createEntity($result);

        $this->assertTrue($new_entity->isEqualTo($entity));
    }

    public function testJsonSerializable()
    {
        $type = new ArticleType;
        $entity = $type->createEntity($this->getExampleValues());

        $this->assertTrue($entity->isValid());

        $json = json_encode($entity);
        $this->assertJson($json);

        $data = json_decode($json, true);
        $new_entity = $type->createEntity($data);
        $this->assertTrue($new_entity->isEqualTo($entity));
    }

    public function testAsValuePath()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity($this->getExampleValues());
        $content_objects = $article->getValue('content_objects');

        $this->assertEquals('content_objects.paragraph[0]', $content_objects[0]->asEmbedPath());
        $this->assertEquals('content_objects.paragraph[1]', $content_objects[1]->asEmbedPath());
        $this->assertEquals('', $article->asEmbedPath());
    }

    public function testCollateChildren()
    {
        $article_type = new ArticleType;
        $article = $article_type->createEntity($this->getExampleValues());
        $content_objects = $article->getValue('content_objects');
        $categories = $article->getValue('categories');

        $entity_map = $article->collateChildren(
            function (EntityInterface $entity) {
                return true;
            }
        );

        $this->assertInstanceOf(EntityMap::CLASS, $entity_map);
        $this->assertEquals(
            [
                'content_objects.paragraph[0]' => $content_objects->getItem(0),
                'content_objects.paragraph[1]' => $content_objects->getItem(1),
                'categories.referenced_category[0]' => $categories->getItem(0)
            ],
            $entity_map->getItems()
        );
    }

    protected function getExampleValues()
    {
        // same order as in ArticleType definition!
        return [
            'uuid' => '7e185d43-f870-46e7-9cea-59800555e970',
            'headline' => 'headline',
            'content' => 'content',
            'click_count' => 123,
            'float' => 123.456,
            'coords' => [ 'lon' => 123.456, 'lat' => 52.34 ],
            'author' => 'Some Author',
            'email' => 'some.author@example.com',
            'website' => 'http://www.example.com',
            'birthday' => '2014-12-31T12:45:55.123456+01:00',
            'images' => [ 1, 2, 3 ],
            'thumbnails' => [ Image::createFromArray($img_data = [
                Image::PROPERTY_LOCATION => 'some.jpg',
                Image::PROPERTY_COPYRIGHT => 'some copyright string',
                Image::PROPERTY_METADATA => [
                    'foo' => 'bar',
                    'leet' => 1337,
                    'bool' => true
                ]
            ]) ],
            'keywords' => [ 'some', 'keywords' ],
            'enabled' => true,
            'content_objects' => [
                [
                    '@type' => 'paragraph',
                    'title' => 'hello world!',
                    'text' => 'hello world from an embedded paragraph',
                    'coords' => [ 'lon' => 12.34, 'lat' => 56.78 ]
                ],
                [
                    '@type' => 'paragraph',
                    'title' => 'hello world again!',
                    'text' => 'hello world from another embedded paragraph',
                    'coords' => [ 'lon' => 12.34, 'lat' => 56.78 ]
                ]
            ],
            'categories'=> [
                [
                    '@type' => 'referenced_category',
                    'identifier' => '1023abf5-f870-46e7-9cea-5980055a523b',
                    'referenced_identifier' => 'some-category'
                ]
            ],
            'meta' => [
                'key' => 'value'
            ],
            'workflow_state' => []
        ];
    }
}
