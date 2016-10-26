<?php

namespace Trellis\Tests\Runtime\Attribute\Date;

use Trellis\Runtime\Attribute\Date\DateAttribute;
use Trellis\Runtime\Attribute\Date\DateValueHolder;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use Trellis\Runtime\EntityTypeInterface;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use stdClass;
use Mockery;

class DateAttributeTest extends TestCase
{
    const ATTR_NAME = 'birthday';

    public function testCreate()
    {
        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $this->assertEquals($attribute->getName(), self::ATTR_NAME);
    }

    public function testIssue28()
    {
        $previous_tz = date_default_timezone_get();
        date_default_timezone_set('Europe/Berlin');

        $datetime = '2016-12-10';
        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $value = $attribute->createValueHolder();
        $value->setValue($datetime);
        $this->assertEquals(
            '2016-12-09T23:00:00.000000+00:00',
            $value->getValue()->format(DateAttribute::FORMAT_ISO8601)
        );

        $date = $value->getValue();
        $date = $date->setTimezone(new \DateTimeZone('Europe/Berlin'));
        $this->assertEquals('2016-12-10T00:00:00.000000+01:00', $date->format(DateAttribute::FORMAT_ISO8601));
        $this->assertEquals('2016-12-10+01:00', $date->format(DateAttribute::FORMAT_ISO8601_DATE));
        $this->assertEquals('2016-12-10', $date->format(DateAttribute::FORMAT_ISO8601_DATE_SIMPLE));

        date_default_timezone_set($previous_tz);
    }

    public function testCreateValueAcceptsString()
    {
        $datetime = '2014-12-31T13:45:55.123+01:00';
        $date_in_utc = '2014-12-31T12:45:55.123000+00:00';
        $date_native = '2014-12-31T12:45:55+00:00';
        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $value = $attribute->createValueHolder();
        $this->assertInstanceOf(DateValueHolder::CLASS, $value);
        $this->assertNull($value->getValue());
        $value->setValue($datetime);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $value->getValue());
        $this->assertEquals($date_in_utc, $value->getValue()->format(DateAttribute::FORMAT_ISO8601));
        $this->assertEquals($date_native, $value->getValue()->format(DateAttribute::FORMAT_NATIVE));
    }

    public function testCreateValueWithDefaultValueAsString()
    {
        $datetime = '2014-12-29+01:00';
        $datetime_in_utc = '2014-12-28T23:00:00.000000+00:00';
        $attribute = new DateAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ DateAttribute::OPTION_DEFAULT_VALUE => $datetime ]
        );
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(DateValueHolder::CLASS, $value);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $value->getValue());
        $this->assertEquals($datetime_in_utc, $value->getValue()->format(DateAttribute::FORMAT_ISO8601));
    }

    public function testCreateValueWithDefaultValueAsStringWithoutDefaultTimezoneForcing()
    {
        $datetime = '2014-12-28T13:45:55.123+01:00';
        $datetime_in_cet = '2014-12-28T13:45:55.123000+01:00';
        $datetime_in_utc = '2014-12-28T12:45:55.123000+00:00';
        $attribute = new DateAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [
                DateAttribute::OPTION_DEFAULT_VALUE => $datetime,
                DateAttribute::OPTION_FORCE_INTERNAL_TIMEZONE => false
            ]
        );
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(DateValueHolder::CLASS, $value);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $value->getValue());
        $this->assertEquals($datetime_in_cet, $value->getValue()->format(DateAttribute::FORMAT_ISO8601));
    }

    public function testDateTimeVsDateTimeImmutableValueComparison()
    {
        $datetime = '2014-12-28+01:00';
        $datetime_in_cet = '2014-12-28T00:00:00.000000+01:00';
        $datetime_in_utc = '2014-12-28T00:00:00.000000+00:00';
        $attribute = new DateAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ DateAttribute::OPTION_DEFAULT_VALUE => $datetime ]
        );
        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(DateValueHolder::CLASS, $valueholder);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $valueholder->getValue());

        $dt1 = new DateTime($datetime);
        $dt2 = new DateTimeImmutable($datetime);

        $this->assertTrue($valueholder->sameValueAs($dt1));
        $this->assertTrue($valueholder->sameValueAs($dt2));
    }

    public function testDefaultValueAcceptsNow()
    {
        $attribute = new DateAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ DateAttribute::OPTION_DEFAULT_VALUE => 'now' ]
        );
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(DateValueHolder::CLASS, $value);
        $this->assertInstanceOf(DateTimeImmutable::CLASS, $value->getValue());
    }

    public function testDefaultValueAcceptsNull()
    {
        $attribute = new DateAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ DateAttribute::OPTION_DEFAULT_VALUE => 'null' ]
        );
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(DateValueHolder::CLASS, $value);
        $this->assertNull($value->getValue());
    }

    public function testDefaultValueAcceptsEmptyString()
    {
        $attribute = new DateAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ DateAttribute::OPTION_DEFAULT_VALUE => '' ]
        );
        $value = $attribute->createValueHolder(true);
        $this->assertInstanceOf(DateValueHolder::CLASS, $value);
        $this->assertNull($value->getValue());
    }

    public function testMinConstraint()
    {
        $datetime_min = '2014-12-28';
        $datetime_foo = '2014-12-27';

        $attribute = new DateAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ DateAttribute::OPTION_MIN_TIMESTAMP => $datetime_min ]
        );
        $valueholder = $attribute->createValueHolder();

        $validation_result = $valueholder->setValue($datetime_foo);

        $this->assertTrue($validation_result->getSeverity() >= IncidentInterface::ERROR);
    }

    public function testMaxConstraint()
    {
        $datetime_max = '2014-12-27';
        $datetime_foo = '2014-12-28';

        $attribute = new DateAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ DateAttribute::OPTION_MAX_TIMESTAMP => $datetime_max ]
        );
        $valueholder = $attribute->createValueHolder();

        $validation_result = $valueholder->setValue($datetime_foo);

        $this->assertTrue($validation_result->getSeverity() >= IncidentInterface::ERROR);
    }

    public function testToNative()
    {
        $datetime = '2014-12-28+01:00';
        $datetime_string = '2014-12-27T23:00:00+00:00';
        $attribute = new DateAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ DateAttribute::OPTION_DEFAULT_VALUE => $datetime ]
        );
        $valueholder = $attribute->createValueHolder(true);

        $this->assertEquals($datetime_string, $valueholder->toNative());
    }

    public function testToNativeRoundtripWithNullValue()
    {
        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $valueholder = $attribute->createValueHolder();
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals('', $valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testToNativeRoundtripWithDefaultValue()
    {
        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $valueholder = $attribute->createValueHolder();

        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());

        $valueholder->setValue($valueholder->toNative());

        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
    }

    public function testToNativeRoundtripWithExplicitTimezoneInput()
    {
        $previous_tz = date_default_timezone_get();
        date_default_timezone_set('Europe/Berlin');

        $date = '2014-12-28+01:00';
        $date_as_native_string = '2014-12-27T23:00:00+00:00';

        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($date);

        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals($date_as_native_string, $valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());

        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals($date_as_native_string, $valueholder->toNative());

        $dt_cet = $valueholder->getValue()->setTimeZone(new DateTimeZone('Europe/Berlin'));
        $valueholder->setValue($dt_cet);

        $dtval = $valueholder->getValue();
        $this->assertNotEquals($attribute->getNullValue(), $dtval);
        $this->assertEquals($date_as_native_string, $valueholder->toNative());
        $this->assertEquals('2014-12-27T23:00:00.000000+00:00', $dtval->format(DateAttribute::FORMAT_ISO8601));
        $this->assertEquals('2014-12-27+00:00', $dtval->format(DateAttribute::FORMAT_ISO8601_DATE));
        $this->assertEquals('2014-12-27', $dtval->format(DateAttribute::FORMAT_ISO8601_DATE_SIMPLE));

        date_default_timezone_set($previous_tz);
    }

    public function testToNativeRoundtrip()
    {
        $previous_tz = date_default_timezone_get();
        date_default_timezone_set('Europe/Berlin');

        $date = '2014-12-28';
        $date_as_native_string = '2014-12-27T23:00:00+00:00';

        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($date);

        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals($date_as_native_string, $valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());

        $this->assertNotEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals($date_as_native_string, $valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());

        $dtval = $valueholder->getValue();
        $this->assertNotEquals($attribute->getNullValue(), $dtval);
        $this->assertEquals('2014-12-27T23:00:00+00:00', $valueholder->toNative());
        $this->assertEquals('2014-12-27T23:00:00.000000+00:00', $dtval->format(DateAttribute::FORMAT_ISO8601));
        $this->assertEquals('2014-12-27+00:00', $dtval->format(DateAttribute::FORMAT_ISO8601_DATE));
        $this->assertEquals('2014-12-27', $dtval->format(DateAttribute::FORMAT_ISO8601_DATE_SIMPLE));

        date_default_timezone_set($previous_tz);
    }
    public function testToNativeCustomFormat()
    {
        $date = '2014-12-28+01:00';
        $date_as_native_string = 'omg2014-12-27.000000';

        $attribute = new DateAttribute(
            self::ATTR_NAME,
            Mockery::mock(EntityTypeInterface::CLASS),
            [ DateAttribute::OPTION_FORMAT_NATIVE => '\o\m\gY-m-d.u' ]
        );
        $valueholder = $attribute->createValueHolder();
        $valueholder->setValue($date);

        $this->assertNotSame($attribute->getNullValue(), $valueholder->getValue());
        $this->assertSame($date_as_native_string, $valueholder->toNative());
    }

    public function testNullVersusEmptyStringValueholderComparison()
    {
        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $vh1 = $attribute->createValueHolder();
        $vh2 = $attribute->createValueHolder();
        $vh1->setValue(null);
        $vh2->setValue('');

        $this->assertTrue($vh1->isEqualTo($vh2), 'Null and empty string datevalueholder should be treated the same');
        $this->assertNull($vh1->toNative());
        $this->assertNull($vh2->toNative());
    }

    public function testEmptyStringHandledAsNull()
    {
        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $valueholder = $attribute->createValueHolder();
        $this->assertTrue($valueholder->isNull());
        $this->assertTrue($valueholder->isDefault());
        $this->assertNull($valueholder->toNative());
        $valueholder->setValue('');
        $this->assertTrue($valueholder->isNull());
        $this->assertTrue($valueholder->isDefault());
        $this->assertSame($attribute->getNullValue(), $valueholder->getValue());
        $this->assertNull($valueholder->toNative());
    }

    public function testNullValueHandling()
    {
        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $valueholder = $attribute->createValueHolder();
        $this->assertTrue($valueholder->isNull());
        $this->assertTrue($valueholder->isDefault());
        $valueholder->setValue(null);
        $this->assertTrue($valueholder->isNull());
        $this->assertTrue($valueholder->isDefault());
        $this->assertNull($valueholder->toNative());
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new DateAttribute(self::ATTR_NAME, Mockery::mock(EntityTypeInterface::CLASS));
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertSame(IncidentInterface::ERROR, $result->getSeverity(), $assert_message);
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
