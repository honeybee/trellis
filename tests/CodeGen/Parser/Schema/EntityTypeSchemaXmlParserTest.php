<?php

namespace Trellis\Tests\CodeGen\Parser\Schema;

use Trellis\Tests\TestCase;
use Trellis\CodeGen\Parser\Schema\EntityTypeSchemaXmlParser;

class EntityTypeSchemaXmlParserTest extends TestCase
{
    public function testParseSchema()
    {
        $type_schema_path = __DIR__ .
            DIRECTORY_SEPARATOR . 'Fixtures' .
            DIRECTORY_SEPARATOR . 'extensive_type_schema.xml';

        $parser = new EntityTypeSchemaXmlParser();
        $type_schema = $parser->parse($type_schema_path);

        $this->assertInstanceOf('\Trellis\CodeGen\Schema\EntityTypeSchema', $type_schema);
    }
}
