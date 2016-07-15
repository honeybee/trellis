<?php

namespace Trellis\Tests\EntityType\Attribute\Url;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Url\Url;
use Trellis\EntityType\Attribute\Url\UrlAttribute;
use Trellis\EntityType\EntityTypeInterface;
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
        $url_attribute = new UrlAttribute('my_url', $entity_type);

        $this->assertInstanceOf(Url::CLASS, $url_attribute->createValue());
        $this->assertInstanceOf(Url::CLASS, $url_attribute->createValue(new Url('http://www.example.com')));
        $this->assertInstanceOf(Url::CLASS, $url_attribute->createValue('https://www.example.com'));
    }
}
