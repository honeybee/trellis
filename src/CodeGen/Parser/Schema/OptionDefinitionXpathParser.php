<?php

namespace Trellis\CodeGen\Parser\Schema;

use Trellis\CodeGen\Schema\OptionDefinitionList;
use Trellis\CodeGen\Schema\OptionDefinition;
use DOMXPath;
use DOMElement;

class OptionDefinitionXpathParser extends XpathParser
{
    protected function parseXpath(DOMXPath $xpath, DOMElement $context)
    {
        $options_list = new OptionDefinitionList();
        $options_nodelist = $xpath->query('./options', $context);

        if ($options_nodelist->length > 0) {
            $option_nodes = $xpath->query('./option', $options_nodelist->item(0));
        } else {
            $option_nodes = $xpath->query('./option', $context);
        }

        foreach ($option_nodes as $option_element) {
            $options_list->addItem(
                $this->parseOption($xpath, $option_element)
            );
        }

        return $options_list;
    }

    protected function parseOption(DOMXPath $xpath, DOMElement $element)
    {
        $name = null;
        $default = null;

        if ($element->hasAttribute('name')) {
            $name = $element->getAttribute('name');
        }

        $nested_options = $xpath->query('./option', $element);
        if ($nested_options->length > 0) {
            $value = new OptionDefinitionList();
            foreach ($nested_options as $option_element) {
                $value->addItem(
                    $this->parseOption($xpath, $option_element)
                );
            }
        } else {
            $value = trim($element->nodeValue);
        }

        return new OptionDefinition(
            array(
                'name' => $name,
                'value' => $this->literalize($value),
                'default' => $this->literalize($default)
            )
        );
    }
}
