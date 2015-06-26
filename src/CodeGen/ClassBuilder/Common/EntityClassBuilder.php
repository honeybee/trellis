<?php

namespace Trellis\CodeGen\ClassBuilder\Common;

class EntityClassBuilder extends ClassBuilder
{
    protected function getTemplate()
    {
        return 'Entity/Entity.twig';
    }

    protected function getImplementor()
    {
        $class_suffix = $this->config->getEntitySuffix('');

        return $this->type_definition->getName() . ucfirst($class_suffix);
    }
}
