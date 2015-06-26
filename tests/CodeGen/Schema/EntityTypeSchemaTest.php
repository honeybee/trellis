<?php

namespace Trellis\Tests\CodeGen\Parser;

use Trellis\Tests\TestCase;
use Trellis\CodeGen\Schema\EntityTypeSchema;
use Trellis\CodeGen\Schema\OptionDefinition;
use Trellis\CodeGen\Parser\Schema\EntityTypeSchemaXmlParser;

class EntityTypeSchemaTest extends TestCase
{
    public function testGetUsedEmbedDefinitions()
    {
        $schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'complex_schema.xml';

        $schema_parser = new EntityTypeSchemaXmlParser();
        $type_schema = $schema_parser->parse($schema_path);

        $embed_defs = $type_schema->getUsedEmbedDefinitions(
            $type_schema->getEntityTypeDefinition()
        );

        $this->assertEquals(1, $embed_defs->getSize());
    }

    public function testGetUsedReferenceDefinitions()
    {
        $schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'complex_schema.xml';

        $schema_parser = new EntityTypeSchemaXmlParser();
        $type_schema = $schema_parser->parse($schema_path);

        $embed_defs = $type_schema->getUsedReferenceDefinitions(
            $type_schema->getEntityTypeDefinition()
        );

        $this->assertEquals(2, $embed_defs->getSize());
    }
}
