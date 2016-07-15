<?php

namespace Trellis\Tests\EntityType\Attribute\Text;

use Trellis\EntityType\Attribute\AttributeInterface;
use Trellis\EntityType\Attribute\Text\Text;
use Trellis\EntityType\Attribute\Text\TextAttribute;
use Trellis\EntityType\EntityTypeInterface;
use Trellis\Tests\TestCase;

class TextAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $text_attribute = new TextAttribute('my_text', $entity_type);

        $this->assertInstanceOf(AttributeInterface::CLASS, $text_attribute);
        $this->assertEquals('my_text', $text_attribute->getName());
        $this->assertEquals($entity_type, $text_attribute->getEntityType());
    }

    public function testCreateValue()
    {
        $entity_type = $this->getMockBuilder(EntityTypeInterface::class)->getMock();
        $text_attribute = new TextAttribute('my_text', $entity_type);

        $this->assertInstanceOf(Text::CLASS, $text_attribute->createValue());
        $this->assertInstanceOf(Text::CLASS, $text_attribute->createValue(new Text('hello world!')));
        $this->assertInstanceOf(Text::CLASS, $text_attribute->createValue('hello world!'));
    }
}
