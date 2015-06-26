<?php

namespace Trellis\Tests\CodeGen\Parser\Schema;

use Trellis\Tests\TestCase;
use Trellis\CodeGen\Parser\Schema\Xpath;
use Trellis\CodeGen\Parser\Schema\Document;
use Trellis\CodeGen\Parser\Schema\OptionDefinitionXpathParser;

class XpathTest extends TestCase
{
    public function testDocumentNamespace()
    {
        $dom_document = new Document('1.0', 'utf-8');
        $dom_document->loadXML(
            <<<XML
<any_container
    xmlns:on="urn:other:namespace"
    xmlns="http://berlinonline.net/trellis/1.0/schema">
    <option name="types">
        <option>Namespaced</option>
        <on:option>OtherNamespace</on:option>
        <option xmlns="">NonNamespaced</option>
    </option>
</any_container>
XML
        );

        $xpath = new Xpath($dom_document, 'customPrefix');
        $parser = new OptionDefinitionXpathParser();
        $option_definitions = $parser->parse(
            $xpath,
            array('context' => $dom_document->documentElement)
        );

        $types_option = $option_definitions[0];
        $types_options_value = $types_option->getValue();

        $this->assertEquals(1, $types_options_value->count());
        $this->assertEquals('Namespaced', $types_options_value[0]->getValue());
    }

    public function testRegisterMultipleNamespacePrefix()
    {
        $dom_document = new Document('1.0', 'utf-8');
        $dom_document->loadXML(
            <<<XML
<any_container
    xmlns="http://berlinonline.net/trellis/1.0/schema"
    xmlns:on="urn:other:namespace"
    xmlns:dt="urn:non-conflicting:prefix:namespace">
    <dt:option name="types">
        <option>Namespaced</option>
        <on:option>OtherNamespace</on:option>
        <option xmlns="">NonNamespaced</option>
    </dt:option>
</any_container>
XML
        );

        $xpath = new Xpath($dom_document, 'prf');
        $xpath->registerNamespace('dt', 'urn:non-conflicting:prefix:namespace');
        $options_nodelist = $xpath->query(
            '/any_container/dt:*/prf:option',
            $dom_document->documentElement
        );

        $this->assertGreaterThan(0, $options_nodelist->length);
        $this->assertEquals('Namespaced', $options_nodelist->item(0)->textContent);
    }

    public function testAlreadyExistentNamespacePrefix()
    {
        // Document has already a namespace prefixed as 'dt' (the default Xpath prefix, if not specified in construtor)

        // 'Namespaced' value should not be retrieved; its prefix
        //      will be the same as the already defined in the parents
        // 'Conflicting' should not be retrieved as well
        //      cause 'dt' will corresponds to two namespaces
        $dom_document = new Document('1.0', 'utf-8');
        $dom_document->loadXML(
            '<any_container
                xmlns="http://berlinonline.net/trellis/1.0/schema"
                xmlns:on="urn:other:namespace"
                xmlns:dt="urn:conflicting:prefix:namespace">
                <dt:option name="types">
                    <option>Namespaced</option>
                    <on:option>OtherNamespace</on:option>
                    <dt:option>Conflicting</dt:option>
                    <option xmlns="">NonNamespaced</option>
                </dt:option>
            </any_container>'
        );

        $xpath = new Xpath($dom_document);
        $xpath->registerNamespace('dt', 'urn:conflicting:prefix:namespace');
        $options_nodelist = $xpath->query(
            '/any_container/dt:option/option',
            $dom_document->documentElement
        );

        $this->assertEquals(0, $options_nodelist->length);
    }
}
