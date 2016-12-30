<?php

namespace Trellis;

interface EntityTypeRelationInterface
{
    /**
     * @return string
     */
    public function getRelatedAttributeName(): string;

    /**
     * @return string
     */
    public function getRelatedEntityTypeClass(): string;
}
