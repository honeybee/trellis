<?php

namespace Trellis\Tests\Runtime\Attribute\UrlList;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\UrlList\UrlListAttribute;
use Trellis\Runtime\Attribute\UrlList\UrlListValueHolder;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Tests\TestCase;
use stdClass;

class UrlListAttributeTest extends TestCase
{
    public function testCreate()
    {
        $attribute = new UrlListAttribute('urllist', $this->getTypeMock());
        $this->assertEquals($attribute->getName(), 'urllist');
    }

    public function testCreateValueWithDefaultValues()
    {
        $data = [ 'http://heise.de', 'https://twitter.com' ];

        $attribute = new UrlListAttribute(
            'urllist',
            $this->getTypeMock(),
            [ UrlListAttribute::OPTION_DEFAULT_VALUE => $data ]
        );

        $valueholder = $attribute->createValueHolder(true);
        $this->assertInstanceOf(UrlListValueHolder::CLASS, $valueholder);
        $this->assertEquals($data, $valueholder->getValue());
    }

    public function testValueComparison()
    {
        $data = [ 'http://heise.de', 'https://twitter.com' ];
        $foo = $data;
        $bar = $data;
        $bar[] = 'https://facebook.com';

        $attribute = new UrlListAttribute(
            'urllist',
            $this->getTypeMock(),
            [ UrlListAttribute::OPTION_DEFAULT_VALUE => $data ]
        );
        $valueholder = $attribute->createValueHolder(true);

        $this->assertEquals($data, $valueholder->getValue());
        $this->assertTrue($valueholder->sameValueAs($foo));
        $this->assertFalse($valueholder->sameValueAs($bar));
    }

    public function testMinMaxStringLengthConstraint()
    {
        $data = [
            'http://heise.de',
        ];

        $attribute = new UrlListAttribute(
            'urllistminmaxstringlength',
            $this->getTypeMock(),
            [
                UrlListAttribute::OPTION_MIN_LENGTH => 3,
                UrlListAttribute::OPTION_MAX_LENGTH => 5
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
        $data = [ 'http://heise.de', 'https://twitter.com' ];

        $attribute = new UrlListAttribute(
            'urllistmaxcount',
            $this->getTypeMock(),
            [ UrlListAttribute::OPTION_MAX_COUNT => 1 ]
        );

        $valueholder = $attribute->createValueHolder();
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($attribute->getDefaultValue(), $attribute->getNullValue());
        $this->assertEquals($attribute->getDefaultValue(), $valueholder->getValue());
        $this->assertEquals($attribute->getNullValue(), $valueholder->getValue());
        $this->assertTrue($validation_result->getSeverity() !== IncidentInterface::SUCCESS);

        $data = [ 'http://heise.de' ];
        $validation_result = $valueholder->setValue($data);
        $this->assertEquals($data, $valueholder->getValue());
        $this->assertFalse($valueholder->isDefault());
        $this->assertFalse($valueholder->isNull());
        $this->assertTrue($validation_result->getSeverity() === IncidentInterface::SUCCESS);
    }

    public function testThrowsOnInvalidDefaultValueInConfig()
    {
        $this->expectException(BadValueException::CLASS);

        $attribute = new UrlListAttribute(
            'urllistinvaliddefaultvalue',
            $this->getTypeMock(),
            [
                UrlListAttribute::OPTION_MIN_LENGTH => 20,
                UrlListAttribute::OPTION_DEFAULT_VALUE => 'http://heise.de'
            ]
        );

        $attribute->getDefaultValue();
    }

    /**
     * @dataProvider provideInvalidValues
     */
    public function testInvalidValue($invalid_value, $assert_message = '')
    {
        $attribute = new UrlListAttribute('urllist', $this->getTypeMock());
        $result = $attribute->getValidator()->validate($invalid_value);
        $this->assertTrue($result->getSeverity() >= IncidentInterface::ERROR, print_r($result->toArray(), true));
    }

    public function provideInvalidValues()
    {
        return [
            [null],
            [false],
            [true],
            [1],
            [new stdClass()],
            [[[]]],
            ['' => 'asdf'],
            ['lol']
        ];
    }
}
