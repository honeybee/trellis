<?php

namespace Trellis\Tests\Attribute\Text;

use Trellis\Attribute\AttributeInterface;
use Trellis\Attribute\Text\TextAttribute;
use Trellis\Entity\EntityTypeInterface;
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
}
