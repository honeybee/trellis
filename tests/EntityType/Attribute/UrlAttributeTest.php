<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Url;
use Trellis\EntityType\Attribute\UrlAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class UrlAttributeTest extends TestCase
{
    /**
     * @var UrlAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertEquals("http://example.com/", $this->attribute->makeValue("http://example.com"));
    }

    public function testMakeValueFromObject(): void
    {
        $this->assertEquals("http://example.com/", $this->attribute->makeValue(new Url("http://example.com")));
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertTrue($this->attribute->makeValue()->isEmpty());
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
        $this->attribute = new UrlAttribute("url", $entity_type);
    }
}
