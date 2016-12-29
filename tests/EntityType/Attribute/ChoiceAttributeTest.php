<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Text;
use Trellis\EntityType\Attribute\ChoiceAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class ChoiceAttributeTest extends TestCase
{
    private const FIXED_OPTIONS = [ "red" => "Red", "blue" => "Blue", "yellow" => "Yellow" ];

    /**
     * @var ChoiceAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $this->assertEquals("blue", $this->attribute->makeValue("blue")->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $this->assertEquals("red", $this->attribute->makeValue(new Text("red"))->toNative());
    }

    /**
     * @expectedException \Trellis\Error\UnexpectedValue
     */
    public function testUnexpectedValue(): void
    {
        $this->attribute->makeValue("purple");
    } // @codeCoverageIgnore

    /**
     * @expectedException \Trellis\Error\AssertionFailed
     */
    public function testMissingOptions(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        new ChoiceAttribute("color", $entity_type);
    } // @codeCoverageIgnore

    protected function setUp(): void
    {
        /* @var EntityTypeInterface $entity_type */
        $entity_type = $this->getMockBuilder(EntityTypeInterface::CLASS)->getMock();
        $this->attribute = new ChoiceAttribute("color", $entity_type, [ "options" => self::FIXED_OPTIONS ]);
    }
}
