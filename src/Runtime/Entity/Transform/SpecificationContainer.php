<?php

namespace Trellis\Runtime\Entity\Transform;

use Trellis\Common\Configurable;

class SpecificationContainer extends Configurable implements SpecificationContainerInterface
{
    /**
     * @var string $name
     */
    protected $name;

    /**
     * @var SpecificationMap $specification_map
     */
    protected $specification_map;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return SpecificationMap
     */
    public function getSpecificationMap()
    {
        return $this->specification_map;
    }

    /**
     * @param mixed $specification_map Either 'SpecificationMap' instance or array suitable for creating one.
     */
    protected function setSpecificationMap($specification_map)
    {
        if ($specification_map instanceof SpecificationMap) {
            $this->specification_map = $specification_map;
        } elseif (is_array($specification_map)) {
            $this->specification_map = new SpecificationMap();
            foreach ($specification_map as $spec_key => $specification) {
                if ($specification instanceof SpecificationInterface) {
                    $this->specification_map->setItem($spec_key, $specification);
                } else {
                    $this->specification_map->setItem(
                        $spec_key,
                        new Specification($specification)
                    );
                }
            }
        } else {
            throw new BadValueException(
                sprintf(
                    'Invalid argument given. Only the types "%s" and "array" are supported.',
                    SpecificationMap::CLASS
                )
            );
        }
    }
}
