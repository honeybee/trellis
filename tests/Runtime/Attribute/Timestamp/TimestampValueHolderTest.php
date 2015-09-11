<?php

namespace Trellis\Tests\Runtime\Attribute\Timestamp;

use Trellis\Runtime\Attribute\Timestamp\TimestampAttribute;
use Trellis\Runtime\Attribute\Timestamp\TimestampValueHolder;
use Trellis\Tests\TestCase;

class TimestampValueHolderTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new TimestampAttribute('publishedAt', $this->getTypeMock());
        $vh = new TimestampValueHolder($attribute);
        $this->assertSame($attribute->getNullValue(), $vh->getValue());

        $attribute = new TimestampAttribute(
            'publishedAt',
            $this->getTypeMock(),
            [ TimestampAttribute::OPTION_DEFAULT_VALUE => 'now' ]
        );
        $vh = $attribute->createValueHolder(true);
        $this->assertNotSame($attribute->getNullValue(), $vh->getValue());
    }

    public function testToNative()
    {
        $datetime = '2014-12-27T12:34:56.789123+01:00';
        $datetime_string = '2014-12-27T11:34:56.789123+00:00';
        $attribute = new TimestampAttribute(
            'birthday',
            $this->getTypeMock(),
            [ TimestampAttribute::OPTION_DEFAULT_VALUE => $datetime ]
        );
        $valueholder = $attribute->createValueHolder(true);

        $this->assertSame($datetime_string, $valueholder->toNative());
    }

    public function testToNativeRoundtripWithNullValue()
    {
        $attribute = new TimestampAttribute('birthday', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $this->assertSame($attribute->getNullValue(), $valueholder->getValue());
        $this->assertNotSame('', $valueholder->toNative());
        $this->assertNull($valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());
        $this->assertSame($attribute->getNullValue(), $valueholder->getValue());
    }

    public function testNullVersusEmptyStringValueholderComparison()
    {
        $attribute = new TimestampAttribute('emptydate', $this->getTypeMock());
        $vh1 = $attribute->createValueHolder();
        $vh2 = $attribute->createValueHolder();
        $vh1->setValue(null);
        $vh2->setValue('');

        $this->assertTrue($vh1->isEqualTo($vh2), 'Null/empty string timestampvalueholder should be treated the same');
        $this->assertNull($vh1->toNative());
        $this->assertNull($vh2->toNative());
        $this->assertNull($vh2->getValue());
    }

    public function testEmptyStringHandledAsNull()
    {
        $attribute = new TimestampAttribute('emptydate', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $this->assertTrue($valueholder->isNull());
        $this->assertTrue($valueholder->isDefault());
        $this->assertNull($valueholder->toNative());
        $valueholder->setValue('');
        $this->assertTrue($valueholder->isNull());
        $this->assertTrue($valueholder->isDefault());
        $this->assertSame($attribute->getNullValue(), $valueholder->getValue());
        $this->assertNull($valueholder->toNative());
        $this->assertNull($valueholder->getValue());
    }

    public function testNullValueHandling()
    {
        $attribute = new TimestampAttribute('emptydate', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $this->assertTrue($valueholder->isNull());
        $this->assertTrue($valueholder->isDefault());
        $valueholder->setValue(null);
        $this->assertTrue($valueholder->isNull());
        $this->assertTrue($valueholder->isDefault());
        $this->assertNull($valueholder->toNative());
        $this->assertNull($valueholder->getValue());
    }

    public function testDifferentValueComparisonSucceeds()
    {
        $attribute = new TimestampAttribute('somedate', $this->getTypeMock());

        $vh1 = $attribute->createValueHolder();
        $vh1->setValue('1985-09-09T22:00:00.000000+00:00');
        $this->assertFalse($vh1->isNull());
        $this->assertFalse($vh1->isDefault());
        $this->assertSame('1985-09-09T22:00:00.000000+00:00', $vh1->getValue()->format('Y-m-d\TH:i:s.uP'));

        $vh2 = $attribute->createValueHolder();
        $vh2->setValue('1985-09-24T13:15:15.000Z');
        $this->assertFalse($vh2->isNull());
        $this->assertFalse($vh2->isDefault());
        $this->assertSame('1985-09-24T13:15:15.000000+00:00', $vh2->getValue()->format('Y-m-d\TH:i:s.uP'));

        $this->assertFalse($vh2->sameValueAs($vh1->toNative()), 'Value comparison w/ native value');
        $this->assertFalse($vh2->sameValueAs($vh1->getValue()), 'Value comparison w/ value');

        $this->assertFalse($vh1->sameValueAs($vh2->toNative()), 'Value comparison w/ native value vice versa');
        $this->assertFalse($vh1->sameValueAs($vh2->getValue()), 'Value comparison w/ value vice versa');
    }

    public function testSameValueComparisonSucceeds()
    {
        $attribute = new TimestampAttribute('somedate', $this->getTypeMock());

        $vh1 = $attribute->createValueHolder();
        $vh1->setValue('2015-09-09T22:00:00.000000+00:00');
        $this->assertFalse($vh1->isNull());
        $this->assertFalse($vh1->isDefault());
        $this->assertSame('2015-09-09T22:00:00.000000+00:00', $vh1->getValue()->format('Y-m-d\TH:i:s.uP'));

        $vh2 = $attribute->createValueHolder();
        $vh2->setValue('2015-09-09T22:00:00.000Z');
        $this->assertFalse($vh2->isNull());
        $this->assertFalse($vh2->isDefault());
        $this->assertSame('2015-09-09T22:00:00.000000+00:00', $vh2->getValue()->format('Y-m-d\TH:i:s.uP'));

        $this->assertTrue($vh2->sameValueAs($vh1->toNative()), 'Value comparison w/ native value');
        $this->assertTrue($vh2->sameValueAs($vh1->getValue()), 'Value comparison w/ value');

        $this->assertTrue($vh1->sameValueAs($vh2->toNative()), 'Value comparison w/ native value vice versa');
        $this->assertTrue($vh1->sameValueAs($vh2->getValue()), 'Value comparison w/ value vice versa');
    }
}
