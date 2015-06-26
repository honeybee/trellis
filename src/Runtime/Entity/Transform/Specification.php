<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\Configurable;

class Specification extends Configurable implements SpecificationInterface
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
