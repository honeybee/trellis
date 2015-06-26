<?php

namespace Trellis\Runtime;

interface ReferencedEntityTypeInterface
{
    public function getReferencedAttributeName();

    public function getReferencedTypeClass();
}
