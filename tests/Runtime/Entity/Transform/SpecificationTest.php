<?php

namespace Trellis\Tests\Runtime\Entity;

use Trellis\Tests\TestCase;
use Trellis\Runtime\Entity\Transform\Specification;

class SpecificationTest extends TestCase
{
    public function testCreate()
    {
        $specification = new Specification($this->getExampleSpec());

        $this->assertInstanceOf('\\Trellis\\Runtime\\Entity\\Transform\\SpecificationInterface', $specification);
        $this->assertEquals('bar', $specification->getName());

        $options = $specification->getOptions();
        $this->assertInstanceOf('\\Trellis\\Common\\Options', $options);
        $this->assertEquals('foo', $options->get('map_as', 'default'));
    }

    protected function getExampleSpec()
    {
        return array(
            'name' => 'bar',
            'options' => array(
                'map_as' => 'foo'
            )
        );
    }
}
