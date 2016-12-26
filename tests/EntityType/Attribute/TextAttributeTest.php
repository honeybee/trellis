<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Text;
use Trellis\EntityType\Attribute\TextAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class TextAttributeTest extends TestCase
{
    /**
     * @var TextAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertEquals("hello world", $this->attribute->makeValue("hello world")->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $this->assertEquals("hello world", $this->attribute->makeValue(new Text("hello world"))->toNative());
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertEquals(Text::EMPTY, $this->attribute->makeValue()->toNative());
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue(5);
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $this->attribute = new TextAttribute("title", $entity_type);
    }
}
