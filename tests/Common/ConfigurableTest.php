<?php

namespace Trellis\Tests\Common;

use Trellis\Common\Configurable;
use Trellis\Common\Options;
use Trellis\Tests\TestCase;

class ConfigurableTest extends TestCase
{
    public function testEmptyFailSafeConfig()
    {
        $p = new Configurable();
        $this->assertEquals(null, $p->getOption('foo'));
    }

    public function testGet()
    {
        $p = new Configurable(array('options' => array('foo' => 'bar')));
        $this->assertEquals('bar', $p->getOption('foo'));
    }

    public function testHas()
    {
        $p = new Configurable(array('options' => array('foo' => 'bar')));
        $this->assertTrue($p->hasOption('foo'));
        $this->assertFalse($p->hasOption('bar'));
    }

    public function testGetValues()
    {
        $p = new Configurable(array('options' => array('foo' => 'bar')));
        $this->assertEquals(array('bar'), $p->getOptionValues());
    }

    public function testGetAsArray()
    {
        $p = new Configurable(array('options' => array('foo' => 'bar')));
        $this->assertEquals(
            array(
                'options' => array('foo' => 'bar'),
                '@type' => 'Trellis\Common\Configurable'
            ),
            $p->toArray()
        );
    }

    /**
     * @expectedException \Trellis\Common\Error\RuntimeException
     */
    public function testSetOptionsThrows()
    {
        $p = new Configurable();
        $p->getOptions()['trololo'] = 'yes';
    }
}
