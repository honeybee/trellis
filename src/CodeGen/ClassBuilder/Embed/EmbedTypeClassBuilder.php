<?php

namespace Trellis\CodeGen\ClassBuilder\Embed;

use Trellis\CodeGen\ClassBuilder\Common\EntityTypeClassBuilder;

class EmbedTypeClassBuilder extends EntityTypeClassBuilder
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
        return $this->type_definition->getName() . ucfirst($this->config->getEmbedTypeSuffix('Type'));
    }
}
