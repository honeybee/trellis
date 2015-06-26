<?php

namespace Trellis\Tests\Runtime\Attribute\Choice;

use Trellis\Runtime\Attribute\Choice\ChoiceAttribute;
use Trellis\Runtime\Attribute\Choice\ChoiceValueHolder;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use Trellis\Runtime\EntityTypeInterface;
use Mockery;

class ChoiceAttributeTest extends TestCase
{
    const ATTR_NAME = 'role';

    public function testCreate()
    {
        $attribute = new ChoiceAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $this->assertEquals($attribute->getName(), self::ATTR_NAME);
    }

    public function testCreateValue()
    {
        $text_attribute = new ChoiceAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $valueholder = $text_attribute->createValueHolder();
        $this->assertInstanceOf(ChoiceValueHolder::CLASS, $valueholder);
        $valueholder->setValue('omgomgomg');
        $this->assertEquals('omgomgomg', $valueholder->getValue());
    }

    public function testValidationSuccess()
    {
        $text_attribute = new ChoiceAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ ChoiceAttribute::OPTION_MIN_LENGTH => 3, ChoiceAttribute::OPTION_MAX_LENGTH => 10 ]
        );

        $result = $text_attribute->getValidator()->validate('erpen derp');
        $this->assertEquals($result->getSeverity(), IncidentInterface::SUCCESS);
    }

    public function testValidationError()
    {
        $text_attribute = new ChoiceAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ ChoiceAttribute::OPTION_MIN_LENGTH => 3, ChoiceAttribute::OPTION_MAX_LENGTH => 5 ]
        );

        $result = $text_attribute->getValidator()->validate('erpen derp');
        $this->assertEquals($result->getSeverity(), IncidentInterface::ERROR);
    }

    public function testAllowedValuesConstraintFails()
    {
        $attribute = new ChoiceAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ ChoiceAttribute::OPTION_ALLOWED_VALUES => [ 'administrator', 'editor' ] ]
        );

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue('foo');
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testAllowedValuesConstraintSucceeds()
    {
        $attribute = new ChoiceAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ ChoiceAttribute::OPTION_ALLOWED_VALUES => [ 'administrator', 'editor' ] ]
        );

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(' editor ');
        $this->assertEquals('editor', $valueholder->getValue());
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($result->getSeverity() === IncidentInterface::SUCCESS);
    }
}
