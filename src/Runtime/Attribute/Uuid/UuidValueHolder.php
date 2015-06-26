<?php

namespace Trellis\Runtime\Attribute\Uuid;

use Trellis\Common\Error\RuntimeException;
use Trellis\Runtime\Attribute\Text\TextValueHolder;

/**
 * Default implementation used for uuid value containment.
 */
class UuidValueHolder extends TextValueHolder
{
    /**
     * Tells whether the valueholder's value is considered to be the same as
     * the default value defined on the attribute.
     *
     * @return boolean
     */
    public function isDefault()
    {
        if ($this->getAttribute()->hasOption(UuidAttribute::OPTION_DEFAULT_VALUE)
            && $this->getAttribute()->getOption(UuidAttribute::OPTION_DEFAULT_VALUE) !== 'auto_gen'
        ) {
            return $this->sameValueAs($this->getAttribute()->getDefaultValue());
        }

        throw new RuntimeException(
            'Operation not supported. A new UUIDv4 is generated for every getNullValue call. No default value set.'
        );
    }
}
