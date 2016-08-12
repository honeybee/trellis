<?php

namespace Trellis\Runtime\Attribute\AssetList;

use Trellis\Runtime\ValueHolder\ListValueHolder;
use Trellis\Runtime\Attribute\Asset\Asset;

/**
 * Holds a list of files.
 */
class AssetListValueHolder extends ListValueHolder
{
    /**
     * Tells whether the given other_value is considered the same value as the
     * internally set value of this valueholder.
     *
     * @param array $other_value values to compare to the internal ones
     *
     * @return boolean true if the given value is considered the same value as the internal one
     */
    protected function valueEquals($other_value)
    {
        if (!is_array($other_value)) {
            return false;
        }

        /** @var array $data */
        $assets = $this->getValue();

        $assets_count = count($assets);
        $other_count = count($other_value);

        if ($assets_count !== $other_count) {
            return false;
        }

        foreach ($assets as $idx => $asset) {
            if (!array_key_exists($idx, $other_value)) {
                return false;
            }

            $other_image = $other_value[$idx];

            $equal = false;
            if (is_array($other_image)) {
                $equal = $asset->similarToArray($other_image);
            } elseif ($other_image instanceof Asset) {
                $equal = $asset->similarTo($other_image);
            }

            if (!$equal) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        if ($this->valueEquals($this->getAttribute()->getNullValue())) {
            return [];
        }

        $assets = [];
        foreach ($this->getValue() as $asset) {
            $assets[] = $asset->toNative();
        }

        return $assets;
    }
}
