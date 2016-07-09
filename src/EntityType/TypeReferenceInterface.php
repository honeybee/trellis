<?php

namespace Trellis\EntityType;

interface TypeReferenceInterface
{
    public function getReferencedAttributeName();

    public function getReferencedTypeClass();
}
