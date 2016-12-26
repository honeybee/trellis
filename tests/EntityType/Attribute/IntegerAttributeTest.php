<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Integer;
use Trellis\EntityType\Attribute\IntegerAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class IntegerAttributeTest extends TestCase
{
    /**
     * @var IntegerAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertEquals(23, $this->attribute->makeValue(23)->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $this->assertEquals(23, $this->attribute->makeValue(new Integer(23))->toNative());
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertEquals(Integer::EMPTY, $this->attribute->makeValue()->toNative());
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue("foobar");
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $this->attribute = new IntegerAttribute("number", $entity_type);
    }
}
