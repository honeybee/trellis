<?php

namespace Trellis\CodeGen\ClassBuilder\Embed;

use Trellis\CodeGen\ClassBuilder\Common\BaseEntityClassBuilder;

class BaseEmbedEntityClassBuilder extends BaseEntityClassBuilder
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
        return $this->type_definition->getName() . ucfirst($this->config->getEmbedEntitySuffix(''));
    }
}
