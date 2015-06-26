<?php

namespace Trellis\CodeGen;

use Trellis\CodeGen\Schema\EntityTypeSchema;

interface PluginInterface
{
    public function execute(EntityTypeSchema $schema);
}
