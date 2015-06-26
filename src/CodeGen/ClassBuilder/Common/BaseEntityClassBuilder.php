<?php

namespace Trellis\CodeGen\ClassBuilder\Common;

class BaseEntityClassBuilder extends EntityClassBuilder
{
    protected function getTemplate()
    {
        return 'Entity/BaseEntity.twig';
    }

    protected function getPackage()
    {
        return $this->type_schema->getPackage() . '\\Base';
    }

    protected function getNamespace()
    {
        return parent::getNamespace() . '\\Base';
    }

    protected function getParentImplementor()
    {
        $parent_class = $this->type_definition->getEntityImplementor();
        if (!$parent_class) {
            $parent_class = sprintf('\\%s\\Entity', self::NS_ENTITY);
        }

        return $parent_class;
    }

    protected function getTemplateVars()
    {
        $entity_class_vars = array('attributes' => $this->prepareAttributeData());

        return array_merge(parent::getTemplateVars(), $entity_class_vars);
    }

    protected function prepareAttributeData()
    {
        $attributes_data = [];

        foreach ($this->type_definition->getAttributes() as $attribute_definition) {
            $attributename = $attribute_definition->getName();

            $attributename_studlycaps = preg_replace_callback(
                '/(?:^|_)(.?)/',
                function ($matches) {
                    return strtoupper($matches[1]);
                },
                $attributename
            );

            $attribute_getter = 'get' . $attributename_studlycaps;
            $attribute_setter = 'set' . $attributename_studlycaps;

            $attributes_data[] = array(
                'name' => $attributename,
                'description' => $attribute_definition->getDescription(),
                'setter' => $attribute_setter,
                'getter' => $attribute_getter,
                'php_type' => 'mixed' // @todo map to php-type
            );
        }

        return $attributes_data;
    }
}
