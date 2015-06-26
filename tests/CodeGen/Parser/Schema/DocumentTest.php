<?php

namespace Trellis\Tests\CodeGen\Parser\Schema;

use Trellis\Tests\TestCase;
use Trellis\CodeGen\Parser\Schema\Xpath;
use Trellis\CodeGen\Parser\Schema\Document;
use Trellis\CodeGen\Parser\Schema\EntityTypeSchemaXmlParser;

class DocumentTest extends TestCase
{
    public function testGenericXinclude()
    {
        $type_schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'extensive_type_schema_include.xml';

        $document = new Document('1.0', 'utf-8');
        $document->load($type_schema_path);

        $document->xinclude();

        $xpath = new Xpath($document);
        $xml_base_nodes = $xpath->query('//@xml:base', $document);

        $this->assertEquals(0, $xml_base_nodes->length);
    }

    public function testSpecificXinclude()
    {
        $type_schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'extensive_type_schema_include.xml';

        $parser = new EntityTypeSchemaXmlParser();
        $type_schema = $parser->parse($type_schema_path);

        $this->assertInstanceOf('\Trellis\CodeGen\Schema\EntityTypeSchema', $type_schema);
    }
}
