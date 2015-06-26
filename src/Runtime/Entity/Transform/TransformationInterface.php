<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\Options;
use Trellis\Runtime\Entity\EntityInterface;

interface TransformationInterface
{
    /**
     * Transform the entity value, which is described by the given attributespec,
     * to it's output representation.
     *
     * @param EntityInterface $entity
     * @param SpecificationInterface $specification
     *
     * @return mixed
     */
    public function apply(EntityInterface $entity, SpecificationInterface $specification);

    /**
     * Transform an incoming value, which is described by the given attributespec,
     * to it's input (entity compatible) representation and set result on the given entity.
     *
     * @param mixed $input_value
     * @param EntityInterface $entity
     * @param SpecificationInterface $specification
     *
     * @return void
     */
    public function revert($input_value, EntityInterface $entity, SpecificationInterface $specification);

    /**
     * @return Options
     */
    public function getOptions();
}
