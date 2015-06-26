<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\Configurable;
use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Entity\EntityInterface;

class Transformer extends Configurable implements TransformerInterface
{
    /**
     * @param EntityInterface $entity
     *
     * @return array
     */
    public function transform(EntityInterface $entity, SpecificationContainerInterface $spec_container)
    {
        $specification_map = $spec_container->getSpecificationMap();
        $transformation = new Transformation();

        $transformed_data = [];
        foreach ($specification_map as $output_key => $specification) {
            $transformed_data[$output_key] = $transformation->apply($entity, $specification);
        }

        return $transformed_data;
    }

    /**
     * @param array $data
     * @param EntityInterface $entity
     *
     * @return void
     */
    public function transformBack(array $data, EntityInterface $entity, SpecificationContainerInterface $spec_container)
    {
        $specification_map = $spec_container->getSpecificationMap();
        $transformation = new Transformation();

        foreach ($specification_map as $output_key => $specification) {
            if (array_key_exists($data, $output_key)) {
                $transformation->revert($entity, $specification, $data[$output_key]);
            }
        }
    }
}
