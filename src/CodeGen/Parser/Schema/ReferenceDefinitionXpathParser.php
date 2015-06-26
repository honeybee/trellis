<?php

namespace Trellis\CodeGen\Parser\Schema;

use Trellis\CodeGen\Schema\EntityTypeDefinitionList;
use Trellis\CodeGen\Schema\ReferenceDefinition;
use DOMXPath;
use DOMElement;

class ReferenceDefinitionXpathParser extends EntityTypeDefinitionXpathParser
{
    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $reference_definitions_list = new EntityTypeDefinitionList();
        $node_list = $xpath->query('//reference_definition', $context);

        foreach ($node_list as $element) {
            $reference_data = $this->parseEntityTypeDefinition($xpath, $element);
            $reference_definition = new ReferenceDefinition($reference_data);
            $reference_definitions_list->addItem($reference_definition);
        }

        return $reference_definitions_list;
    }
}
