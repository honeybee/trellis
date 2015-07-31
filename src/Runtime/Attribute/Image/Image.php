<?php

namespace Trellis\Runtime\Attribute\Image;

use Trellis\Runtime\ValueHolder\ComplexValue;

class Image extends ComplexValue
{
    const PROPERTY_LOCATION = 'location';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CAPTION = 'caption';
    const PROPERTY_COPYRIGHT = 'copyright';
    const PROPERTY_COPYRIGHT_URL = 'copyright_url';
    const PROPERTY_SOURCE = 'source';
    const PROPERTY_AOI = 'aoi';
    const PROPERTY_META_DATA = 'meta_data';

    protected $values = [
        self::PROPERTY_LOCATION => '',
        self::PROPERTY_TITLE => '',
        self::PROPERTY_CAPTION => '',
        self::PROPERTY_COPYRIGHT => '',
        self::PROPERTY_COPYRIGHT_URL => '',
        self::PROPERTY_SOURCE => '',
        self::PROPERTY_AOI => '',
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
            self::PROPERTY_COPYRIGHT_URL => self::VALUE_TYPE_URL,
            self::PROPERTY_SOURCE => self::VALUE_TYPE_TEXT,
            self::PROPERTY_AOI => self::VALUE_TYPE_TEXT,
            self::PROPERTY_META_DATA => self::VALUE_TYPE_ARRAY
        ];
    }

    public function getLocation()
    {
        return $this->values[self::PROPERTY_LOCATION];
    }

    public function getTitle()
    {
        return $this->values[self::PROPERTY_TITLE];
    }

    public function getCaption()
    {
        return $this->values[self::PROPERTY_CAPTION];
    }

    public function getCopyright()
    {
        return $this->values[self::PROPERTY_COPYRIGHT];
    }

    public function getCopyrightUrl()
    {
        return $this->values[self::PROPERTY_COPYRIGHT_URL];
    }

    public function getSource()
    {
        return $this->values[self::PROPERTY_SOURCE];
    }

    public function getAoi()
    {
        return $this->values[self::PROPERTY_AOI];
    }

    public function getMetaData()
    {
        return $this->values[self::PROPERTY_META_DATA];
    }

    public function __toString()
    {
        return $this->location;
    }
}
