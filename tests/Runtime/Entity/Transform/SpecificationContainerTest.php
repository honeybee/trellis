<?php

namespace Trellis\Tests\Runtime\Entity;

use Trellis\Tests\TestCase;
use Trellis\Runtime\Entity\Transform\SpecificationContainer;

class SpecificationContainerTest extends TestCase
{
    public function testCreate()
    {
        $spec_container = new SpecificationContainer($this->getExampleSpec());

        $this->assertInstanceOf(
            '\\Trellis\\Runtime\\Entity\\Transform\\SpecificationContainerInterface',
            $spec_container
        );
        $this->assertEquals('embed', $spec_container->getName());

        $options = $spec_container->getOptions();
        $this->assertInstanceOf('\\Trellis\\Common\\Options', $options);
        $this->assertEquals(array('foo' => 'bar', 'blah' => 'blub'), $options->toArray());

        $this->assertInstanceOf(
            '\\Trellis\\Runtime\\Entity\\Transform\\SpecificationMap',
            $spec_container->getSpecificationMap()
        );
    }

    protected function getExampleSpec()
    {
        return array(
            'name' => 'embed',
            'options' => array(
                'foo' => 'bar',
                'blah' => 'blub'
            ),
            'specification_map' => array(
                'voting_stats' => array(
                    'name' => 'voting_stats',
                    'options' => array(
                        'map_as' => 'voting_average',
                        'value' => 'expression:"foo" ~ "BAR"',
                        'getter' => 'getVotingAverage',
                        'setter' => 'setVotingAverage',
                        'input' => false,
                        'output' => true
                    )
                )
            )
        );
    }
}
