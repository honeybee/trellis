<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\ConfigurableInterface;
use Trellis\Common\Object;

interface SpecificationInterface extends ConfigurableInterface
{
    /**
     * @return string
     */
    public function getName();
}
