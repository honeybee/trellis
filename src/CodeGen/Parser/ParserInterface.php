<?php

namespace Trellis\CodeGen\Parser;

use Trellis\CodeGen\Schema\EntityTypeDefinitionList;
use Trellis\CodeGen\Schema\EmbedDefinition;

interface ParserInterface
{
    /**
     * Parses the given source and returns the result.
     *
     * @return mixed
     */
    public function parse($source, array $options = []);
}
