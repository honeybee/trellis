<?php

namespace Trellis\Runtime\Attribute;

interface HandlesFileInterface
{
    const FILETYPE_FILE = 'file';
    const FILETYPE_IMAGE = 'image';
    const FILETYPE_VIDEO = 'video';

    /**
     * Returns the property name that is used to store a file identifier.
     *
     * This property may be used for input field names in HTML and should then
     * be used in the file metadata value object as a property name for storing
     * a relative file path or similar.
     *
     * @return string property name
     */
    public function getFileLocationPropertyName();

    /**
     * @return string type identifier of file type handled by the attribute
     */
    public function getFiletypeName();
}
