<?php

namespace Trellis\CodeGen\ClassBuilder\Embed;

use Trellis\CodeGen\ClassBuilder\Common\EntityClassBuilder;

class EmbedEntityClassBuilder extends EntityClassBuilder
{
    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Embed';
    }

    protected function getNamespace()
    {
        return $this->type_schema->getNamespace() . '\\Embed';
    }

    protected function getImplementor()
    {
        return $this->type_definition->getName() . ucfirst($this->config->getEmbedEntitySuffix(''));
    }
}
