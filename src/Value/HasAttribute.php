<?php

namespace Trellis\Value;

trait HasAttribute
{
    /**
     * @var AttributeInterface $attribute
     */
    protected $attribute;

    /**
     * @return AttributeInterface
     */
    public function getAttribute()
    {
        return $this->attribute;
    }
}
