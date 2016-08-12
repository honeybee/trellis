<?php

namespace Trellis\Runtime\Attribute\AssetList;

use Trellis\Runtime\Attribute\Asset\AssetRule;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Entity\EntityInterface;

class AssetListRule extends Rule
{
    protected function execute($values, EntityInterface $entity = null)
    {
        if (!is_array($values)) {
            $this->throwError('non_array_value', [], IncidentInterface::CRITICAL);
            return false;
        }

        $sanitized = [];

        $asset_rule = new AssetRule('asset', $this->getOptions());

        foreach ($values as $index => $val) {
            if (!$asset_rule->apply($val)) {
                $this->throwIncidentsAsErrors($asset_rule, null, [ 'path_parts' => [ $index ] ]);
                return false;
            }

            $sanitized[] = $asset_rule->getSanitizedValue();
        }

        $this->setSanitizedValue($sanitized);

        return true;
    }
}
