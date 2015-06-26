<?php

namespace Trellis\CodeGen\ClassBuilder\Common;

class EntityTypeClassBuilder extends ClassBuilder
{
    protected function getTemplate()
    {
        return 'EntityType/EntityType.twig';
    }

    protected function getImplementor()
    {
        $class_suffix = $this->config->getTypeSuffix('Type');

        return $this->type_definition->getName() . ucfirst($class_suffix);
    }
}
