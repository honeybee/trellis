<?php

namespace Trellis\Runtime\Attribute\ImageList;

use Trellis\Runtime\Attribute\Image\ImageRule;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Entity\EntityInterface;

class ImageListRule extends Rule
{
    protected function execute($values, EntityInterface $entity = null)
    {
        if (!is_array($values)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        $sanitized = [];

        $image_rule = new ImageRule('image', $this->getOptions());

        foreach ($values as $index => $val) {
            if (!$image_rule->apply($val)) {
                $this->throwIncidentsAsErrors($image_rule, null, [ 'path_parts' => [ $index ] ]);
                return false;
            }

            $sanitized[] = $image_rule->getSanitizedValue();
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }
}
