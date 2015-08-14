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
}
