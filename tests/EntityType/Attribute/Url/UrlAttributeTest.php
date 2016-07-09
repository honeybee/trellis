<?php

namespace Trellis\Tests\EntityType\Attribute\Url;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Url\Url;
use Trellis\EntityType\Attribute\Url\UrlAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Entity\EntityInterface;
use Trellis\Tests\TestCase;

class UrlAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $url_attribute = new UrlAttribute('my_url', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $url_attribute);
        $this->assertEquals('my_url', $url_attribute->getName());
        $this->assertEquals($entity_type, $url_attribute->getEntityType());
    }

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $entity = $this->getMockBuilder(EntityInterface::class)->getMock();
        $url_attribute = new UrlAttribute('my_url', $entity_type);

        $this->assertInstanceOf(Url::CLASS, $url_attribute->createValue($entity));
        $this->assertInstanceOf(Url::CLASS, $url_attribute->createValue($entity, new Url('http://www.example.com')));
        $this->assertInstanceOf(Url::CLASS, $url_attribute->createValue($entity, 'https://www.example.com'));
    }
}
