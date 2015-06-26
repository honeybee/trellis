<?php

namespace Trellis\Tests\CodeGen\Parser\Schema;

use Trellis\Tests\TestCase;
use Trellis\CodeGen\Parser\Schema\OptionDefinitionXpathParser;
use Trellis\CodeGen\Parser\Schema\Xpath;
use Trellis\CodeGen\Parser\Schema\Document;

class OptionDefinitionXpathParserTest extends TestCase
{
    public function testOneNestedOptions()
    {
        $dom_document = new Document('1.0', 'utf-8');
        $dom_document->loadXML(
            '<random_container xmlns="http://berlinonline.net/trellis/1.0/schema">
                <option name="types">
                    <option>VotingStats</option>
                </option>
            </random_container>'
        );

        $xpath = new Xpath($dom_document);
        $parser = new OptionDefinitionXpathParser();
        $option_definitions = $parser->parse(
            $xpath,
            array('context' => $dom_document->documentElement)
        );

        $types_option = $option_definitions[0];
        $types_options_value = $types_option->getValue();

        $this->assertInstanceOf(
            'Trellis\CodeGen\Schema\OptionDefinitionList',
            $option_definitions
        );
        $this->assertInstanceOf(
            'Trellis\CodeGen\Schema\OptionDefinitionList',
            $types_option->getValue()
        );
        $this->assertEquals(1, $option_definitions->getSize());
        $this->assertEquals('VotingStats', $types_options_value[0]->getValue());
    }
}
