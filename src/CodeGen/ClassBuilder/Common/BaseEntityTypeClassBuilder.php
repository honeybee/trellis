<?php

namespace Trellis\CodeGen\ClassBuilder\Common;

use Trellis\CodeGen\Schema\AttributeDefinition;
use Trellis\CodeGen\Schema\OptionDefinitionList;

class BaseEntityTypeClassBuilder extends EntityTypeClassBuilder
{
    protected function getTemplate()
    {
        return 'EntityType/BaseEntityType.twig';
    }

    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Base';
    }

    protected function getNamespace()
    {
        return $this->type_schema->getNamespace() . '\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_implementor = $this->type_definition->getImplementor();
        if ($parent_implementor === null) {
            $parent_implementor = sprintf('%s\\EntityType', self::NS_MODULE);
        }

        return $parent_implementor;
    }

    protected function getTemplateVars()
    {
        $type_class_vars = array(
            'attributes' => $this->prepareAttributeData(),
            'entity_implementor' => $this->getEntityImplementor(),
            'type_name' => $this->type_definition->getName(),
            'options' => $this->preRenderOptions($this->type_definition->getOptions(), 12)
        );

        return array_merge(parent::getTemplateVars(), $type_class_vars);
    }

    protected function getEntityImplementor()
    {
        $namespace_parts = explode('\\', $this->getNamespace());
        array_pop($namespace_parts);
        return var_export(
            sprintf(
                '\\%s\\%s',
                implode('\\', $namespace_parts),
                $this->type_definition->getName() . ucfirst($this->config->getEmbedEntitySuffix(''))
            ),
            true
        );
    }

    protected function prepareAttributeData()
    {
        $attributes_data = [];

        foreach ($this->type_definition->getAttributes() as $attribute_definition) {
            $attribute_implementor = $attribute_definition->getImplementor();

            if ($attribute_definition->getShortName() === 'embedded-entity-list') {
                $this->expandEmbedNamespaces($attribute_definition);
            } elseif ($attribute_definition->getShortName() === 'entity-reference-list') {
                $this->expandReferenceNamespaces($attribute_definition);
            }

            $attributename = $attribute_definition->getName();
            $attributes_data[] = array(
                'implementor' => var_export($attribute_implementor, true),
                'class_name' => $attribute_implementor,
                'name' => $attributename,
                'options' => $this->preRenderOptions($attribute_definition->getOptions(), 20)
            );
        }

        return $attributes_data;
    }

    protected function expandEmbedNamespaces(AttributeDefinition $attribute_definition)
    {
        $type_options = $attribute_definition->getOptions()->filterByName('entity_types');
        if ($type_options) {
            foreach ($type_options->getValue() as $type_option) {
                $type_option->setValue(
                    sprintf(
                        '\\%s\\Embed\\%s%s',
                        $this->getRootNamespace(),
                        $type_option->getValue(),
                        $this->config->getEmbedEntitySuffix('Type')
                    )
                );
            }
        }
    }

    protected function expandReferenceNamespaces(AttributeDefinition $attribute_definition)
    {
        $reference_options = $attribute_definition->getOptions()->filterByName('entity_types');
        if ($reference_options) {
            foreach ($reference_options->getValue() as $reference_option) {
                $reference_option->setValue(
                    sprintf(
                        '\\%s\\Reference\\%s%s',
                        $this->getRootNamespace(),
                        $reference_option->getValue(),
                        $this->config->getEmbedEntitySuffix('Type')
                    )
                );
            }
        }
    }

    protected function preRenderOptions(OptionDefinitionList $options, $initial_indent = 0, $indent_size = 4)
    {
        if ($options->getSize() === 0) {
            return '[]';
        }

        $options_code = array('array(');
        $indent_spaces = str_repeat(" ", $initial_indent + $indent_size);
        $next_level_indent = $initial_indent + $indent_size;
        foreach ($options as $option) {
            $option_name = $option->getName();
            $option_value = $option->getValue();
            if ($option_name && $option_value instanceof OptionDefinitionList) {
                $options_code[] = sprintf(
                    "%s'%s' => %s,",
                    $indent_spaces,
                    $option_name,
                    $this->preRenderOptions($option_value, $next_level_indent)
                );
            } elseif ($option_value instanceof OptionDefinitionList) {
                $options_code[] = sprintf(
                    "%s%s,",
                    $indent_spaces,
                    $this->preRenderOptions($option_value, $next_level_indent)
                );
            } elseif ($option_name) {
                $options_code[] = sprintf(
                    "%s'%s' => %s,",
                    $indent_spaces,
                    $option_name,
                    var_export($option_value, true)
                );
            } else {
                $options_code[] = sprintf("%s%s,", $indent_spaces, var_export($option_value, true));
            }
        }
        $options_code[] = sprintf('%s)', str_repeat(" ", $initial_indent));

        return implode(PHP_EOL, $options_code);
    }
}
