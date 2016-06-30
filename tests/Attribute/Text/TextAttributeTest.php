<?php

namespace Trellis\Tests\Attribute;

use Trellis\Attribute\AttributeInterface;
use Trellis\Attribute\Text\TextAttribute;
use Trellis\Entity\EntityTypeInterface;
use Trellis\Tests\TestCase;

class TextAttributeTest extends TestCase
{
    public function testConstruct()
    {
        $entityType = $this->getMockBuilder(EntityTypeInterface::class)->getMock();

        $attribute = new TextAttribute('foobar', $entityType);

        $this->assertInstanceOf(AttributeInterface::CLASS, $attribute);
        $this->assertEquals('foobar', $attribute->getName());
        $this->assertEquals($entityType, $attribute->getEntityType());
    }
}
