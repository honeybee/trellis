<?php

namespace Trellis\CodeGen\Parser\Schema;

use Trellis\Common\Error\RuntimeException;
use Trellis\CodeGen\Schema\EntityTypeDefinition;
use DOMXPath;
use DOMElement;

class EntityTypeDefinitionXpathParser extends XpathParser
{
    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $node_list = $xpath->query('./type_definition', $context);

        if ($node_list->length === 0) {
            throw new RuntimeException(
                "Missing type_definition node. Please check the given type_schema."
            );
        }

        return new EntityTypeDefinition(
            $this->parseEntityTypeDefinition($xpath, $node_list->item(0))
        );
    }

    protected function parseEntityTypeDefinition(DOMXPath $xpath, DOMElement $element)
    {
        $implementor = null;
        $implementor_list = $xpath->query('./implementor', $element);
        if ($implementor_list->length > 0) {
            $implementor = $implementor_list->item(0)->nodeValue;
        }

        $entity_implementor = null;
        $entity_implementor_list = $xpath->query('./entity_implementor', $element);
        if ($entity_implementor_list->length > 0) {
            $entity_implementor = $entity_implementor_list->item(0)->nodeValue;
        }

        $description_node = $xpath->query('./description', $element)->item(0);
        if ($description_node) {
            $description = $this->parseDescription(
                $xpath,
                $xpath->query('./description', $element)->item(0)
            );
        } else {
            $description = '';
        }

        return [
            'name' => $element->getAttribute('name'),
            'implementor' => $implementor,
            'entity_implementor' => $entity_implementor,
            'description' => $description,
            'options' => $this->parseOptions($xpath, $element),
            'attributes' => $this->parseAttributes($xpath, $element)
        ];
    }

    protected function parseAttributes(DOMXPath $xpath, DOMElement $element)
    {
        $parser = new AttributeDefinitionXpathParser();
        $attributes_element = $xpath->query('./attributes', $element)->item(0);

        if ($attributes_element) {
            return $parser->parse($xpath, [ 'context' => $attributes_element ]);
        }
        return [];
    }
}
