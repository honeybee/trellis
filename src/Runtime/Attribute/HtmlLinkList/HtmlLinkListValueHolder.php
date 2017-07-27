<?php

namespace Trellis\Runtime\Attribute\HtmlLinkList;

use Trellis\Runtime\ValueHolder\ListValueHolder;
use Trellis\Runtime\Attribute\HtmlLink\HtmlLink;

/**
 * Holds a list of HtmlLinks.
 */
class HtmlLinkListValueHolder extends ListValueHolder
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
        $links = $this->getValue();

        $links_count = count($links);
        $other_count = count($other_value);

        if ($links_count !== $other_count) {
            return false;
        }

        foreach ($links as $idx => $link) {
            if (!array_key_exists($idx, $other_value)) {
                return false;
            }

            $other_link = $other_value[$idx];

            $equal = false;
            if (is_array($other_link)) {
                $equal = $link->similarToArray($other_link);
            } elseif ($other_link instanceof HtmlLink) {
                $equal = $link->similarTo($other_link);
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

        $links = [];
        foreach ($this->getValue() as $link) {
            $links[] = $link->toNative();
        }

        return $links;
    }
}
