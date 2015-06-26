<?php

namespace Trellis\Runtime\Attribute\EntityReferenceList;

use Trellis\Runtime\Attribute\EmbeddedEntityList\EmbeddedEntityListRule;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Common\Error\RuntimeError;
use Trellis\Runtime\Entity\EntityReferenceInterface;

/**
 * Validates that a given value consistently translates to a list of entities.
 *
 * Supported options: entity_types
 */
class EntityReferenceListRule extends EmbeddedEntityListRule
{
}
