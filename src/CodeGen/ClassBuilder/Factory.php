<?php

namespace Trellis\CodeGen\ClassBuilder;

use Trellis\CodeGen\Config;
use Trellis\Common\Object;
use Trellis\CodeGen\Schema\EntityTypeSchema;
use Trellis\CodeGen\Schema\EntityTypeDefinition;
use Trellis\CodeGen\ClassBuilder\Common\BaseEntityTypeClassBuilder;
use Trellis\CodeGen\ClassBuilder\Common\EntityTypeClassBuilder;
use Trellis\CodeGen\ClassBuilder\Common\BaseEntityClassBuilder;
use Trellis\CodeGen\ClassBuilder\Common\EntityClassBuilder;
use Trellis\CodeGen\ClassBuilder\Embed\BaseEmbedTypeClassBuilder;
use Trellis\CodeGen\ClassBuilder\Embed\EmbedTypeClassBuilder;
use Trellis\CodeGen\ClassBuilder\Embed\BaseEmbedEntityClassBuilder;
use Trellis\CodeGen\ClassBuilder\Embed\EmbedEntityClassBuilder;
use Trellis\CodeGen\ClassBuilder\Reference\BaseReferenceTypeClassBuilder;
use Trellis\CodeGen\ClassBuilder\Reference\ReferenceTypeClassBuilder;
use Trellis\CodeGen\ClassBuilder\Reference\BaseReferenceEntityClassBuilder;
use Trellis\CodeGen\ClassBuilder\Reference\ReferenceEntityClassBuilder;

class Factory extends Object
{
    protected $type_schema;

    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getEntityTypeSchema()
    {
        return $this->type_schema;
    }

    public function setEntityTypeSchema(EntityTypeSchema $type_schema)
    {
        $this->type_schema = $type_schema;
    }

    public function createClassBuildersForType(EntityTypeDefinition $type)
    {
        switch (get_class($type)) {
            case 'Trellis\CodeGen\Schema\EmbedDefinition':
                $class_builders = $this->createEmbedClassBuilders($type);
                break;
            case 'Trellis\CodeGen\Schema\ReferenceDefinition':
                $class_builders = $this->createReferenceClassBuilders($type);
                break;
            default:
                $class_builders = $this->createDefaultClassBuilders($type);
                break;
        }

        return $class_builders;
    }

    protected function createDefaultClassBuilders(EntityTypeDefinition $type)
    {
        $builder_properties = array(
            'type_schema' => $this->type_schema,
            'type_definition' => $type,
            'config' => $this->config
        );
        return array(
            new BaseEntityTypeClassBuilder($builder_properties),
            new EntityTypeClassBuilder($builder_properties),
            new BaseEntityClassBuilder($builder_properties),
            new EntityClassBuilder($builder_properties)
        );
    }

    protected function createEmbedClassBuilders(EntityTypeDefinition $embed_type_def)
    {
        $builder_properties = array(
            'type_schema' => $this->type_schema,
            'type_definition' => $embed_type_def,
            'config' => $this->config
        );
        return array(
            new BaseEmbedTypeClassBuilder($builder_properties),
            new EmbedTypeClassBuilder($builder_properties),
            new BaseEmbedEntityClassBuilder($builder_properties),
            new EmbedEntityClassBuilder($builder_properties)
        );
    }

    protected function createReferenceClassBuilders(EntityTypeDefinition $reference_type_def)
    {
        $builder_properties = array(
            'type_schema' => $this->type_schema,
            'type_definition' => $reference_type_def,
            'config' => $this->config
        );
        return array(
            new BaseReferenceTypeClassBuilder($builder_properties),
            new ReferenceTypeClassBuilder($builder_properties),
            new BaseReferenceEntityClassBuilder($builder_properties),
            new ReferenceEntityClassBuilder($builder_properties)
        );
    }
}
