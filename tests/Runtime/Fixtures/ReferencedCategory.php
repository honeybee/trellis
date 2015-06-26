<?php

namespace Trellis\Tests\Runtime\Fixtures;

use Trellis\Common\Options;
use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Attribute\Text\TextAttribute;
use Trellis\Runtime\Attribute\Textarea\TextareaAttribute;
use Trellis\Runtime\EntityType;
use Trellis\Runtime\Entity\Entity;
use Trellis\Runtime\Entity\EntityReferenceInterface;

class ReferencedCategory extends Entity implements EntityReferenceInterface
{
    public function getIdentifier()
    {
        return $this->getValue('identifier');
    }

    public function getReferencedIdentifier()
    {
        return $this->getValue('referenced_identifier');
    }
}
