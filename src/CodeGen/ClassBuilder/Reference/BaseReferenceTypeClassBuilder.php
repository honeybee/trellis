<?php

namespace Trellis\CodeGen\ClassBuilder\Reference;

use Trellis\CodeGen\ClassBuilder\Common\BaseEntityTypeClassBuilder;

class BaseReferenceTypeClassBuilder extends BaseEntityTypeClassBuilder
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
        return $this->type_definition->getName() . ucfirst($this->config->getReferencedTypeSuffix('Type'));
    }

    protected function getTemplate()
    {
        return 'EntityType/BaseReferencedEntityType.twig';
    }
}
