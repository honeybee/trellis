<?php

namespace Trellis\CodeGen\ClassBuilder\Embed;

use Trellis\CodeGen\ClassBuilder\Common\BaseEntityTypeClassBuilder;

class BaseEmbedTypeClassBuilder extends BaseEntityTypeClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Embed\\Base';
    }

    protected function getNamespace()
    {
        return $this->type_schema->getNamespace() . '\\Embed\\Base';
    }

    protected function getImplementor()
    {
        return $this->type_definition->getName() . ucfirst($this->config->getEmbedTypeSuffix('Type'));
    }

    protected function getTemplate()
    {
        return 'EntityType/BaseEmbeddedEntityType.twig';
    }
}
