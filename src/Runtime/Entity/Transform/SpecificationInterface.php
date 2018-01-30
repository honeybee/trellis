<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\ConfigurableInterface;

interface SpecificationInterface extends ConfigurableInterface
{
    /**
     * @return string
     */
    public function getName();
}
