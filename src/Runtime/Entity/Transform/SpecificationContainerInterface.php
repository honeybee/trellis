<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\Options;

interface SpecificationContainerInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return SpecificationMap
     */
    public function getSpecificationMap();

    /**
     * @return Options
     */
    public function getOptions();
}
