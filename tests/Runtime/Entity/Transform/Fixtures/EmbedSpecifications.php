<?php

namespace Trellis\Tests\Runtime\Entity\Transform\Fixtures;

use Trellis\Tests\TestCase;
use Trellis\Common\Options;
use Trellis\Runtime\Entity\Transform\Transformer;
use Trellis\Runtime\Entity\Transform\SpecificationContainer;
use Trellis\Runtime\Entity\Transform\SpecificationMap;
use Trellis\Runtime\Entity\Transform\Specification;

/**
 * An AttributeSpecifications base implementation as would be created by the code-generation.
 */
class EmbedSpecifications extends SpecificationContainer
{
    public function __construct(array $state = [])
    {
        $specification_map = new SpecificationMap();
        $specification_map->setItems(
            array(
                'title' => new Specification(
                    array(
                        'name' => 'title',
                        'options' => array(
                            'attribute' => 'headline'
                        )
                    )
                ),
                'author' => new Specification(
                    array(
                        'name' => 'author'
                    )
                )
            )
        );

        return parent::__construct(
            array(
                'name' => 'embed',
                'options' => [],
                'specification_map' => $specification_map
            )
        );
    }
}
