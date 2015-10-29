<?php

namespace Trellis\Runtime\Attribute;

interface HandlesFileInterface
{
    const FILETYPE_FILE = 'file';
    const FILETYPE_IMAGE = 'image';
    const FILETYPE_VIDEO = 'video';

    const DEFAULT_PROPERTY_LOCATION = 'location';
    const DEFAULT_PROPERTY_FILESIZE = 'filesize';
    const DEFAULT_PROPERTY_FILENAME = 'filename';
    const DEFAULT_PROPERTY_MIMETYPE = 'mimetype';

    /**
     * Returns the property name that is used to store a file identifier. This
     * file identifier might be an arbitrary technical identifier that's got
     * nothing to do with the eventual or original filename being used.
     *
     * This property may be used for input field names in HTML and should then
     * be used in the file metadata value object as a property name for storing
     * a relative file path or similar.
     *
     * @return string property name
     */
    public function getFileLocationPropertyName();

    /**
     * @return string property name for filesize in byte of the handled file
     */
    public function getFileSizePropertyName();

    /**
     * @return string property name for filename storage of the handled file
     */
    public function getFileNamePropertyName();

    /**
     * @return string property name for mimetype storage of the handled file
     */
    public function getFileMimetypePropertyName();

    /**
     * @return string type identifier for the file handled by the attribute
     */
    public function getFiletypeName();
}
