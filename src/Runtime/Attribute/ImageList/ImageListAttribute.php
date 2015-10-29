<?php

namespace Trellis\Runtime\Attribute\ImageList;

use Trellis\Runtime\Attribute\HandlesFileListInterface;
use Trellis\Runtime\Attribute\Image\Image;
use Trellis\Runtime\Attribute\ListAttribute;

/**
 * A list of images (that is, their metadata including a location).
 */
class ImageListAttribute extends ListAttribute implements HandlesFileListInterface
{
    protected function buildValidationRules()
    {
        $rules = parent::buildValidationRules();

        $options = $this->getOptions();

        $rule = new ImageListRule('valid-image-list', $options);

        $rules->push($rule);

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
        return Image::PROPERTY_LOCATION;
    }

    /**
     * @return string property name for filesize in byte of the handled file
     */
    public function getFileSizePropertyName()
    {
        return Image::PROPERTY_FILESIZE;
    }

    /**
     * @return string property name for filename storage of the handled file
     */
    public function getFileNamePropertyName()
    {
        return Image::PROPERTY_FILENAME;
    }

    /**
     * @return string property name for mimetype storage of the handled file
     */
    public function getFileMimetypePropertyName()
    {
        return Image::PROPERTY_MIMETYPE;
    }

    /**
     * @return string type identifier of file type handled by the attribute
     */
    public function getFiletypeName()
    {
        return self::FILETYPE_IMAGE;
    }
}
