<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\Options;
use Trellis\Runtime\Entity\EntityInterface;

interface TransformerInterface
{
    /**
     * @param EntityInterface $entity
     * @param SpecificationContainerInterface $spec_container
     *
     * @return array
     */
    public function transform(EntityInterface $entity, SpecificationContainerInterface $spec_container);

    /**
     * @param array $data
     * @param EntityInterface $entity
     * @param SpecificationContainerInterface $spec_container
     *
     * @return void
     */
    public function transformBack(
        array $data,
        EntityInterface $entity,
        SpecificationContainerInterface $spec_container
    );

    /**
     * @return Options
     */
    public function getOptions();
}
