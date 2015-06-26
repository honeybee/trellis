<?php

namespace Trellis\Runtime\Attribute\Image;

use Trellis\Runtime\Attribute\Attribute;
use Trellis\Runtime\Attribute\HandlesFileInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\RuleList;

/**
 * An asset (file metadata including a location).
 */
class AssetAttribute extends Attribute implements HandlesFileInterface
{
    protected function buildValidationRules()
    {
        $rules = new RuleList();

        $options = $this->getOptions();

        $rules->push(new AssetRule('valid-asset', $options));

        return $rules;
    }

    /**
     * Returns the property name that is used to store a file identifier.
     *
     * This property may be used for input field names in HTML and should then
     * be used in the file metadata value object as a property name for storing
     * a relative file path or similar.
     *
     * @return string property name
     */
    public function getFileLocationPropertyName()
    {
        return Asset::PROPERTY_LOCATION;
    }

    /**
     * @return string type identifier of file type handled by the attribute
     */
    public function getFiletypeName()
    {
        return self::FILETYPE_FILE;
    }
}
