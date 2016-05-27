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

    public function getEmbeddedTypeByReferencedPrefix($referenced_prefix)
    {
        $found_types = $this->getEmbeddedEntityTypeMap()->filter(
            function ($entity_type) use ($referenced_prefix) {
                $type_prefix = $entity_type->getOption('referenced_type_prefix', $entity_type->getPrefix());
                return $type_prefix === $referenced_prefix;
            }
        )->getValues();

        return count($found_types) == 1 ? $found_types[0] : null;
    }
}
