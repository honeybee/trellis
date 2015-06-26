<?php

namespace Trellis\Tests\Runtime\Attribute\Timestamp;

use Trellis\Runtime\Attribute\Timestamp\TimestampAttribute;
use Trellis\Runtime\Attribute\Timestamp\TimestampValueHolder;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use DateTime;
use DateTimeImmutable;
use stdClass;

class TimestampAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new TimestampAttribute('publishedAt', $this->getTypeMock());
        $this->assertEquals($attribute->getName(), 'publishedAt');
    }

    public function testCreateValueAcceptsString()
    {
        $datetime = '2014-12-31T13:45:55.123+01:00';
        $datetime_in_utc = '2014-12-31T12:45:55.123000+00:00';
        $attribute = new TimestampAttribute('publishedAt', $this->getTypeMock());
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertNull($value->getValue());
        $value->setValue($datetime);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $value->getValue());
        $this->assertEquals($datetime_in_utc, $value->getValue()->format(TimestampAttribute::FORMAT_ISO8601));
    }

    public function testCreateValueWithDefaultValueAsString()
    {
        $datetime = '2014-12-29T13:45:55.123+01:00';
        $datetime_in_utc = '2014-12-29T12:45:55.123000+00:00';
        $attribute = new TimestampAttribute(
            'publishedAt',
            $this->getTypeMock(),
            [ TimestampAttribute::OPTION_DEFAULT_VALUE => $datetime ]
        );
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $value->getValue());
        $this->assertEquals($datetime_in_utc, $value->getValue()->format(TimestampAttribute::FORMAT_ISO8601));
    }

    public function testCreateValueWithDefaultValueAsStringWithoutDefaultTimezoneForcing()
    {
        $datetime = '2014-12-28T13:45:55.123+01:00';
        $datetime_in_cet = '2014-12-28T13:45:55.123000+01:00';
        $datetime_in_utc = '2014-12-28T12:45:55.123000+00:00';
        $attribute = new TimestampAttribute(
            'publishedAt',
            $this->getTypeMock(),
            [
                TimestampAttribute::OPTION_DEFAULT_VALUE => $datetime,
                TimestampAttribute::OPTION_FORCE_INTERNAL_TIMEZONE => false
            ]
        );
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $value->getValue());
        $this->assertEquals($datetime_in_cet, $value->getValue()->format(TimestampAttribute::FORMAT_ISO8601));
    }

    public function testDateTimeVsDateTimeImmutableValueComparison()
    {
        $datetime = '2014-12-28T13:45:55.123+01:00';
        $datetime_in_cet = '2014-12-28T13:45:55.123000+01:00';
        $datetime_in_utc = '2014-12-28T12:45:55.123000+00:00';
        $attribute = new TimestampAttribute(
            'publishedAt',
            $this->getTypeMock(),
            [ TimestampAttribute::OPTION_DEFAULT_VALUE => $datetime ]
        );
        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $valueholder);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $valueholder->getValue());

        $dt1 = new DateTime($datetime);
        $dt2 = new DateTimeImmutable($datetime);

        $this->assertTrue($valueholder->sameValueAs($dt1));
        $this->assertTrue($valueholder->sameValueAs($dt2));
    }

    public function testSameValueAsWithString()
    {
        $datetime1 = '2014-12-31T13:45:55.123+01:00';
        $datetime1_in_utc = '2014-12-31T12:45:55.123000+00:00';
        $datetime2 = '2014-12-31T13:45:55.123+01:00';
        $datetime2_in_utc = '2014-12-31T12:45:55.123000+00:00';
        $attribute = new TimestampAttribute('publishedAt', $this->getTypeMock());
        $value = $attribute->createValueHolder();
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $value->setValue($datetime1);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $value->getValue());
        $this->assertEquals($datetime1_in_utc, $value->getValue()->format(TimestampAttribute::FORMAT_ISO8601));

        $this->assertTrue($value->sameValueAs($datetime2));
        $this->assertTrue($value->sameValueAs($datetime2_in_utc));
    }

    public function testDefaultValueAcceptsNow()
    {
        $attribute = new TimestampAttribute(
            'publishedAt',
            $this->getTypeMock(),
            [ TimestampAttribute::OPTION_DEFAULT_VALUE => 'now' ]
        );
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $value->getValue());
    }

    public function testDefaultValueAcceptsNull()
    {
        $attribute = new TimestampAttribute(
            'publishedAt',
            $this->getTypeMock(),
            [ TimestampAttribute::OPTION_DEFAULT_VALUE => 'null' ]
        );
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertNull($value->getValue());
    }

    public function testDefaultValueAcceptsEmptyString()
    {
        $attribute = new TimestampAttribute(
            'publishedAt',
            $this->getTypeMock(),
            [ TimestampAttribute::OPTION_DEFAULT_VALUE => '' ]
        );
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(TimestampValueHolder::CLASS, $value);
        $this->assertNull($value->getValue());
    }

    public function testMinConstraint()
    {
        $datetime_min = '2014-12-28T13:45:55.123+01:00';
        $datetime_foo = '2014-12-28T13:45:55.023+01:00';

        $attribute = new TimestampAttribute(
            'publishedAt',
            $this->getTypeMock(),
            [ TimestampAttribute::OPTION_MIN_TIMESTAMP => $datetime_min ]
        );
        $valueholder = $attribute->createValueHolder();

        $validation_result = $valueholder->setValue($datetime_foo);

        $this->assertTrue($validation_result->getSeverity() >= IncidentInterface::ERROR);
    }

    public function testMaxConstraint()
    {
        $datetime_max = '2014-12-28T13:45:55.123+01:00';
        $datetime_foo = '2014-12-28T12:45:55.234+00:00';

        $attribute = new TimestampAttribute(
            'publishedAt',
            $this->getTypeMock(),
            [ TimestampAttribute::OPTION_MAX_TIMESTAMP => $datetime_max ]
        );
        $valueholder = $attribute->createValueHolder();

        $validation_result = $valueholder->setValue($datetime_foo);

        $this->assertTrue($validation_result->getSeverity() >= IncidentInterface::ERROR);
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new TimestampAttribute('publishedAt', $this->getTypeMock());
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertEquals(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
    }

    public function provideInvalidValues()
    {
        return [
            [ false ],
            [ true ],
            [ [] ],
            [ new stdClass() ],
            [ 1 ]
        ];
    }
}
