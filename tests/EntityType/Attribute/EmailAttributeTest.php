<?php

namespace Trellis\Tests\EntityType\Attribute;

use Trellis\Entity\ValueObject\Email;
use Trellis\Entity\ValueObject\Text;
use Trellis\EntityType\Attribute\EmailAttribute;
use Trellis\EntityTypeInterface;
use Trellis\Tests\TestCase;

final class EmailAttributeTest extends TestCase
{
    /**
     * @var EmailAttribute $attribute
     */
    private $attribute;

    public function testMakeValueFromNative(): void
    {
        $email = $this->attribute->makeValue("peter.parker@example.com");
        $this->assertEquals("peter.parker@example.com", $email->toNative());
    }

    public function testMakeValueFromObject(): void
    {
        $email = new Email("peter.parker@example.com");
        $this->assertEquals("peter.parker@example.com", $this->attribute->makeValue($email)->toNative());
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
        $this->attribute = new EmailAttribute("email", $entity_type);
    }
}
