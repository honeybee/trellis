<?php

namespace Trellis\Runtime\Attribute\Date;

use Trellis\Runtime\Attribute\Timestamp\TimestampValueHolder;
use DateTimeInterface;

/**
 * Default implementation used for date value containment.
 */
class DateValueHolder extends TimestampValueHolder
{
    /**
     * Returns a (de)serializable representation of the internal value. The
     * returned format MUST be acceptable as a new value on the valueholder
     * to reconstitute it.
     *
     * This serializes with time information and timezone to allow correct
     * interpretation when deserializing even though the time itself is not
     * relevant.
     *
     * @return mixed value that can be used for serializing/deserializing
     */
    public function toNative()
    {
        if (!$this->getValue() instanceof DateTimeInterface) {
            return null;
        }

        return $this->getValue()->format(
            $this->getAttribute()->getOption(
                DateAttribute::OPTION_FORMAT_NATIVE,
                DateAttribute::FORMAT_NATIVE
            )
        );
    }
}
