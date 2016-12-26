<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Decimal;
use Trellis\EntityType\Attribute\DecimalAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class DecimalAttributeTest extends TestCase
{
    /**
     * @var DecimalAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertEquals(42.5, $this->attribute->makeValue(42.5)->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $this->assertEquals(42.5, $this->attribute->makeValue(new Decimal(42.5))->toNative());
    }

    public function testMakeEmptyValue(): void
    {
        $this->assertEquals(Decimal::EMPTY, $this->attribute->makeValue()->toNative());
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
        $this->attribute = new DecimalAttribute("ratio", $entity_type);
    }
}
