<?php

namespace Trellis\Runtime\Attribute\Asset;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\ValueHolder\ComplexValue;

class Asset extends ComplexValue
{
    const PROPERTY_LOCATION = 'location';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CAPTION = 'caption';
    const PROPERTY_COPYRIGHT = 'copyright';
    const PROPERTY_COPYRIGHT_URL = 'copyright_url';
    const PROPERTY_SOURCE = 'source';
    const PROPERTY_FILESIZE = 'filesize';
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_MIMETYPE = 'mimetype';
    const PROPERTY_META_DATA = 'meta_data';

    protected $values = [
        self::PROPERTY_LOCATION => '',
        self::PROPERTY_TITLE => '',
        self::PROPERTY_CAPTION => '',
        self::PROPERTY_COPYRIGHT => '',
        self::PROPERTY_COPYRIGHT_URL => '',
        self::PROPERTY_SOURCE => '',
        self::PROPERTY_FILESIZE => 0,
        self::PROPERTY_FILENAME => '',
        self::PROPERTY_MIMETYPE => '',
        self::PROPERTY_META_DATA => []
    ];

    public static function getMandatoryPropertyNames()
    {
        return [
            self::PROPERTY_LOCATION
        ];
    }

    public static function getPropertyMap()
    {
        return [
            self::PROPERTY_LOCATION => self::VALUE_TYPE_TEXT,
            self::PROPERTY_TITLE => self::VALUE_TYPE_TEXT,
            self::PROPERTY_CAPTION => self::VALUE_TYPE_TEXT,
            self::PROPERTY_COPYRIGHT => self::VALUE_TYPE_TEXT,
            self::PROPERTY_COPYRIGHT_URL => self::VALUE_TYPE_TEXT,
            self::PROPERTY_SOURCE => self::VALUE_TYPE_TEXT,
            self::PROPERTY_FILESIZE => self::VALUE_TYPE_INTEGER,
            self::PROPERTY_FILENAME => self::VALUE_TYPE_TEXT,
            self::PROPERTY_MIMETYPE => self::VALUE_TYPE_TEXT,
            self::PROPERTY_META_DATA => self::VALUE_TYPE_ARRAY
        ];
    }

    /**
     * Creates a new instance.
     *
     * @param array $data key value pairs to create the value object from
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $loc = trim($this->values[self::PROPERTY_LOCATION]);
        if (empty($loc)) {
            throw new BadValueException('Empty "' . self::PROPERTY_LOCATION . '" property given.');
        }
    }

    public function __toString()
    {
        return $this->location;
    }
}
