<?php

namespace Trellis\Runtime\Attribute\EntityReferenceList;

use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListAttribute;
use Trellis\Runtime\Entity\EntityReferenceInterface;
use Trellis\Common\Error\RuntimeException;
use ReflectionClass;

/**
 * Allows to nest multiple types under a defined attribute_name.
 *
 * The corresponding internal value is a list of entities.
 *
 * Supported options: OPTION_ENTITY_TYPES (to specify allowed entity types)
 */
class EntityReferenceListAttribute extends EmbeddedEntityListAttribute
{
    protected function createEmbeddedTypeMap()
    {
        $entity_type_map = parent::createEmbeddedTypeMap();

        foreach ($entity_type_map as $embedded_type) {
            $entity_implementor = $embedded_type->getEntityImplementor();
            $entity_reflection = new ReflectionClass($entity_implementor);
            if (!$entity_reflection->implementsInterface(EntityReferenceInterface::CLASS)) {
                throw new RuntimeException(
                    sprintf(
                        'Invalid reference-type (%s) given to %s. Only instance of %s accepted.',
                        $this->getName(),
                        $entity_implementor,
                        EntityReferenceInterface::CLASS
                    )
                );
            }
        }

        return $entity_type_map;
    }
}
