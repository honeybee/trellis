<?php

namespace Trellis;

interface EntityTypeRelationInterface
{
    public function getRelatedAttributeName(): string;

    public function getRelatedEntityTypeClass(): string;
}
