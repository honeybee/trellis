<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\Configurable;
use Trellis\Runtime\Entity\EntityInterface;

class Transformation extends Configurable implements TransformationInterface
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
    public function apply(EntityInterface $entity, SpecificationInterface $specification)
    {
        $attribute_name = $specification->getOption('attribute', $specification->getName());
        $entity_value = $entity->getValue($attribute_name);

        return $entity_value;
    }

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
    public function revert($input_value, EntityInterface $entity, SpecificationInterface $specification)
    {
        $attribute_name = $specification->getOption('attribute', $specification->getName());
        $entity->setValue($attribute_name, $input_value);
    }
}
