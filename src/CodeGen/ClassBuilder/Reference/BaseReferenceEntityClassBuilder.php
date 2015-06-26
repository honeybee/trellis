<?php

namespace Trellis\CodeGen\ClassBuilder\Reference;

use Trellis\CodeGen\ClassBuilder\Common\BaseEntityClassBuilder;

class BaseReferenceEntityClassBuilder extends BaseEntityClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Reference\\Base';
    }

    protected function getNamespace()
    {
        return $this->type_schema->getNamespace() . '\\Reference\\Base';
    }

    protected function getImplementor()
    {
        return $this->type_definition->getName() . ucfirst($this->config->getReferencedEntitySuffix(''));
    }
}
