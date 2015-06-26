<?php

namespace Trellis\Tests\Runtime\Attribute\EmailList;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\EmailList\EmailListAttribute;
use Trellis\Runtime\Attribute\EmailList\EmailListValueHolder;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use Trellis\Runtime\EntityTypeInterface;
use stdClass;
use Mockery;

class EmailListAttributeTest extends TestCase
{
    const ATTR_NAME = 'emails';

    public function testCreate()
    {
        $attribute = new EmailListAttribute(self::ATTR_NAME, $this->getTypeMock());
        $this->assertEquals($attribute->getName(), self::ATTR_NAME);
    }

    public function testCreateValueWithDefaultValues()
    {
        $data = [ 'foo@bar.com' => 'bar' ];

        $attribute = new EmailListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmailListAttribute::OPTION_DEFAULT_VALUE => $data ]
        );

        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(EmailListValueHolder::CLASS, $valueholder);
        $this->assertEquals($data, $valueholder->getValue());
    }

    public function testCastToArrayWhenSettingSingleValueWorks()
    {
        $attribute = new EmailListAttribute(self::ATTR_NAME, $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue('foo@bar.com');
        $this->assertEquals([ 'foo@bar.com' => '' ], $valueholder->getValue());
    }

    public function testSettingInvalidValueFails()
    {
        $data = [ 'foobarcom' => 'bar' ];

        $attribute = new EmailListAttribute(self::ATTR_NAME, $this->getTypeMock());

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue($data);
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testValueComparison()
    {
        $data = [ 'foo@bar.com' => 'bar' ];
        $foo = $data;
        $bar = $data;
        $bar['asdf@example.com'] = 'omgomgomg';

        $attribute = new EmailListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmailListAttribute::OPTION_DEFAULT_VALUE => $data ]
        );
        $valueholder = $attribute->createValueHolder(true);

        $this->assertEquals($data, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($foo));
        $this->assertFalse($valueholder->sameValueAs($bar));
    }

    public function testMinMaxStringLengthConstraint()
    {
        $data = [
            'bar@foo.com' => '15',
            'foo@bar.com' => '1234567890',
        ];

        $attribute = new EmailListAttribute(
            'emailslabellength',
            $this->getTypeMock(),
            [
                EmailListAttribute::OPTION_MIN_EMAIL_LABEL_LENGTH => 3,
                EmailListAttribute::OPTION_MAX_EMAIL_LABEL_LENGTH => 5
            ]
        );

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);

        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());

        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testMaxCountConstraint()
    {
        $data = [ 'foo@bar.com' => 'bar', 'blah@exmaple.com' => 'blub' ];

        $attribute = new EmailListAttribute(
            'emailsmaxcount',
            $this->getTypeMock(),
            [ EmailListAttribute::OPTION_MAX_COUNT => 1 ]
        );

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($attribute->getDefaultValue(), $attribute->getNullValue());
        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);

        $data = [ 'foo@bar.com' => 'bar' ];
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($data, $valueholder->getValue());
        $this->assertFalse($valueholder->isDefault());
        $this->assertFalse($valueholder->isNull());
        $this->assertTrue($validation_result->getSeverity() === IncidentInterface::SUCCESS);
    }

    public function testToNativeRoundtripWithBooleanFlags()
    {
        $emails = [ 'foo@bar.com' => 'some name', 'blah@blub.com' => 'yeah right' ];
        $attribute = new EmailListAttribute(self::ATTR_NAME, $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($emails);
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals($emails, $valueholder->getValue());
        $this->assertEquals($emails, $valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());
        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals($emails, $valueholder->toNative());
        $this->assertEquals($emails, $valueholder->getValue());
    }

    public function testAllowedLabelsConstraintFails()
    {
        $attribute = new EmailListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmailListAttribute::OPTION_ALLOWED_EMAIL_LABELS => [ 'bar' ] ]
        );

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(['foo@bar.com' => 'blah']);
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testAllowedEmailsConstraintFails()
    {
        $attribute = new EmailListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmailListAttribute::OPTION_ALLOWED_EMAILS => [ 'foo@bar.com' => 'asdf' ] ]
        );

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(['bar@foo.com' => 'asdf']);
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testAllowedPairsConstraintFails()
    {
        $attribute = new EmailListAttribute(
            self::ATTR_NAME,
            $this->getTypeMock(),
            [ EmailListAttribute::OPTION_ALLOWED_EMAIL_PAIRS => [ 'foo@bar.com' => 'foo' ] ]
        );

        $valueholder = $attribute->createValueHolder();
        $result = $valueholder->setValue(['foo@bar.de' => 'foo', 'foo@bar.com' => 'fo' ]);
        $this->assertTrue($result->getSeverity() !== IncidentInterface::SUCCESS);
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);

        $attribute = new EmailListAttribute(
            'emailinvalidintegerdefaultvalue',
            $this->getTypeMock(),
            [
                EmailListAttribute::OPTION_MIN_EMAIL_LABEL_LENGTH => 1,
                EmailListAttribute::OPTION_MAX_EMAIL_LABEL_LENGTH => 5,
                EmailListAttribute::OPTION_DEFAULT_VALUE => [ 'email@example.com' => '1234567890' ]
            ]
        );

        $attribute->getDefaultValue();
    }

    public function testThrowsOnInvalidEmailDefaultValueInConfig()
    {
        $this->setExpectedException(BadValueException::CLASS);

        $attribute = new EmailListAttribute(
            'emailinvaliddefaultvalue',
            $this->getTypeMock(),
            [ EmailListAttribute::OPTION_DEFAULT_VALUE => [ 'emailexample.com' => '1234567890' ] ]
        );

        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new EmailListAttribute(self::ATTR_NAME, $this->getTypeMock());
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return [
            [ null ],
            [ false ],
            [ true ],
            [ 1 ],
            [ '' => 'asdf' ]
        ];
    }
}
