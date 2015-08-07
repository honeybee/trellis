<?php

namespace Trellis\Runtime\Attribute;

/**
 * Marker interface for attributes that have a valueholder that
 * holds a ComplexValueInterface implementing ComplexValue instance.
 *
 * @see GeoPoint and related valueholder, rule and attribute
 *
 * @todo should we add methods like getComplexValueImplementor() or filterEmptyPayload()?
 */
interface HasComplexValueInterface
{
}
