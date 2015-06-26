<?php

namespace Trellis\Tests\Runtime\Entity\Transform\Fixtures;

use Trellis\Tests\TestCase;
use Trellis\Common\Options;
use Trellis\Runtime\Entity\Transform\Transformer;

/**
 * An Transformer base implementation as would be created by the code-generation.
 */
class TestTransformer extends Transformer
{
    public function __construct(array $state = [])
    {
        parent::__construct(
            array_merge(
                $state,
                array(
                    'options' => array(
                        'foo' => 'bar',
                    )
                )
            )
        );
    }
}
