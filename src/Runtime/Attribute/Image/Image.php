<?php

namespace Trellis\Runtime\Attribute\Image;

use Trellis\Common\Error\BadValueException;
use Trellis\Runtime\Attribute\HandlesFileInterface;
use Trellis\Runtime\ValueHolder\ComplexValue;

class Image extends ComplexValue
{
    const PROPERTY_LOCATION = HandlesFileInterface::DEFAULT_PROPERTY_LOCATION;
    const PROPERTY_FILESIZE = HandlesFileInterface::DEFAULT_PROPERTY_FILESIZE;
    const PROPERTY_FILENAME = HandlesFileInterface::DEFAULT_PROPERTY_FILENAME;
    const PROPERTY_MIMETYPE = HandlesFileInterface::DEFAULT_PROPERTY_MIMETYPE;
    const PROPERTY_TITLE = 'title';
    const PROPERTY_CAPTION = 'caption';
    const PROPERTY_COPYRIGHT = 'copyright';
    const PROPERTY_COPYRIGHT_URL = 'copyright_url';
    const PROPERTY_SOURCE = 'source';
    const PROPERTY_WIDTH = 'width';
    const PROPERTY_HEIGHT = 'height';
    const PROPERTY_AOI = 'aoi';
    const PROPERTY_METADATA = 'metadata';

    protected $values = [
        self::PROPERTY_LOCATION => '',
        self::PROPERTY_FILESIZE => 0,
        self::PROPERTY_FILENAME => '',
        self::PROPERTY_MIMETYPE => '',
        self::PROPERTY_TITLE => '',
        self::PROPERTY_CAPTION => '',
        self::PROPERTY_COPYRIGHT => '',
        self::PROPERTY_COPYRIGHT_URL => '',
        self::PROPERTY_SOURCE => '',
        self::PROPERTY_WIDTH => 0,
        self::PROPERTY_HEIGHT => 0,
        self::PROPERTY_AOI => '',
        self::PROPERTY_METADATA => []
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
            self::PROPERTY_FILESIZE => self::VALUE_TYPE_INTEGER,
            self::PROPERTY_FILENAME => self::VALUE_TYPE_TEXT,
            self::PROPERTY_MIMETYPE => self::VALUE_TYPE_TEXT,
            self::PROPERTY_TITLE => self::VALUE_TYPE_TEXT,
            self::PROPERTY_CAPTION => self::VALUE_TYPE_TEXT,
            self::PROPERTY_COPYRIGHT => self::VALUE_TYPE_TEXT,
            self::PROPERTY_COPYRIGHT_URL => self::VALUE_TYPE_URL,
            self::PROPERTY_SOURCE => self::VALUE_TYPE_TEXT,
            self::PROPERTY_WIDTH => self::VALUE_TYPE_INTEGER,
            self::PROPERTY_HEIGHT => self::VALUE_TYPE_INTEGER,
            self::PROPERTY_AOI => self::VALUE_TYPE_TEXT,
            self::PROPERTY_METADATA => self::VALUE_TYPE_ARRAY
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

    public function getWidth()
    {
        return $this->values[self::PROPERTY_WIDTH];
    }

    public function getHeight()
    {
        return $this->values[self::PROPERTY_HEIGHT];
    }

    public function getFilesize()
    {
        return $this->values[self::PROPERTY_FILESIZE];
    }

    public function getFilename()
    {
        return $this->values[self::PROPERTY_FILENAME];
    }

    public function getMimetype()
    {
        return $this->values[self::PROPERTY_MIMETYPE];
    }

    public function getAoi()
    {
        return $this->values[self::PROPERTY_AOI];
    }

    public function getMetadata()
    {
        return $this->values[self::PROPERTY_METADATA];
    }

    public function __toString()
    {
        return $this->values[self::PROPERTY_LOCATION];
    }
}
