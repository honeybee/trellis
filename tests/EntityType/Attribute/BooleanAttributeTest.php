<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Boolean;
use Trellis\EntityType\Attribute\BooleanAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class BooleanAttributeTest extends TestCase
{
    /**
     * @var BooleanAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertTrue($this->attribute->makeValue(true)->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $this->assertTrue($this->attribute->makeValue(new Boolean(true))->toNative());
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertFalse($this->attribute->makeValue()->toNative());
    }

    public function testHasParam(): void
    {
        $this->assertTrue($this->attribute->hasParam("foo"));
        $this->assertFalse($this->attribute->hasParam("bar"));
    }

    public function testGetParam(): void
    {
        $this->assertEquals("bar", $this->attribute->getParam("foo"));
        $this->assertNull($this->attribute->getParam("bar"));
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue(23);
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $this->attribute = new BooleanAttribute("active", $entity_type, [ "foo" => "bar" ]);
    }
}
