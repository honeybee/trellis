<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Uuid;
use Trellis\EntityType\Attribute\UuidAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class UuidAttributeTest extends TestCase
{
    /**
     * @var UuidAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertEquals(
            "110ec58a-a0f2-4ac4-8393-c866d813b8d1",
            $this->attribute->makeValue("110ec58a-a0f2-4ac4-8393-c866d813b8d1")
        );
    }

    public function testMakeValueFromObject(): void
    {
        $this->assertEquals(
            "110ec58a-a0f2-4ac4-8393-c866d813b8d1",
            $this->attribute->makeValue(new Uuid("110ec58a-a0f2-4ac4-8393-c866d813b8d1"))
        );
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
        $this->attribute = new UuidAttribute("uuid", $entity_type);
    }
}
