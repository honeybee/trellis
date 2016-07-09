<?php

namespace Trellis\Runtime;

interface TypeReferenceInterface
{
    public function getReferencedAttributeName();

    public function getReferencedTypeClass();
}
