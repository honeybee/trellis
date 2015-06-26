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
        $this->assertEquals($attribute->getNullValue(), $vh->getValue());

        $attribute = new TimestampAttribute(
            'publishedAt',
            $this->getTypeMock(),
            [ TimestampAttribute::OPTION_DEFAULT_VALUE => 'now' ]
        );
        $vh = $attribute->createValueHolder(true);
        $this->assertNotEquals($attribute->getNullValue(), $vh->getValue());
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

        $this->assertEquals($datetime_string, $valueholder->toNative());
    }

    public function testToNativeRoundtripWithNullValue()
    {
        $attribute = new TimestampAttribute('birthday', $this->getTypeMock());
        $valueholder = $attribute->createValueHolder();
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertEquals('', $valueholder->toNative());

        $valueholder->setValue($valueholder->toNative());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
    }
}
