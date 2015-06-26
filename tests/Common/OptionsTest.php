<?php

namespace Trellis\Tests\Common;

use Trellis\Common\Options;
use Trellis\Tests\TestCase;

class OptionsTest extends TestCase
{
    public function testConstruct()
    {
        $p = new Options();

        $this->assertEquals(0, count($p->toArray()));
        $this->assertEquals(0, $p->count());
        $this->assertEmpty($p->getKeys());
    }

    public function testCreate()
    {
        $data = array(
            'str' => 'some string',
            'int' => mt_rand(0, 999),
            'bool' => (mt_rand(1, 100) <= 50) ? true : false,
        );

        $options = new Options($data);

        $this->assertInstanceOf('\\Trellis\\Common\\Options', $options);
        $this->assertEquals($data['str'], $options->get('str'));
        $this->assertEquals($data['int'], $options->get('int'));
        $this->assertEquals($data['bool'], $options->get('bool'));
    }

    /**
     * @expectedException \Trellis\Common\Error\BadValueException
     */
    public function testToArrayFails()
    {
        $options = new Options(array('omg' => $this));
        $options->toArray();
    }

    /**
     * @expectedException \Trellis\Common\Error\RuntimeException
     */
    public function testSetValueViaArrayAccessFails()
    {
        $options = new Options();
        $options['obj'] = 'asdf';
    }

    /**
     * @expectedException \Trellis\Common\Error\RuntimeException
     */
    public function testSetValueViaPropertyAccessFails()
    {
        $options = new Options();
        $options->obj = 'asdf';
    }

    /**
     * @expectedException \Trellis\Common\Error\RuntimeException
     */
    public function testAppendFails()
    {
        $options = new Options();
        $options->append(array('foo' => 'omg'));
    }

    /**
     * @expectedException \Trellis\Common\Error\RuntimeException
     */
    public function testUnsetFails()
    {
        $options = new Options(array('foo' => 'bar'));
        unset($options['foo']);
    }

    /**
     * @expectedException \Trellis\Common\Error\RuntimeException
     */
    public function testExchangeArrayFails()
    {
        $options = new Options();
        $options->exchangeArray(array('foo' => 'omg'));
    }
}
