<?php

namespace Trellis\EntityType\Attribute\Image;

use Trellis\EntityType\Attribute\Asset\Asset;
use Trellis\EntityType\Attribute\IntegerList\IntegerList;
use Trellis\EntityType\Attribute\Integer\Integer;
use Trellis\EntityType\Attribute\KeyValueList\KeyValueList;
use Trellis\EntityType\Attribute\Text\Text;

class Image extends Asset
{
    /**
     * @var Integer $width
     */
    private $width;

    /**
     * @var Integer $height
     */
    private $height;

    /**
     * @var IntegerList $aoi
     */
    private $aoi;

    /**
     * {@inheritdoc}
     */
    public static function getPropertyMap()
    {
        return array_merge(
            parent::getPropertyMap(),
            [
                'width' => Integer::NIL,
                'height' => Integer::NIL,
                'aoi' => IntegerList::NIL
            ]
        );
    }

    /**
     * @param string $location
     * @param string $filename
     * @param int $filesize
     * @param string $mimetype
     * @param string $source
     * @param string $title
     * @param string $caption
     * @param string $copyright
     * @param string $copyright_url
     * @param mixed[] $metadata
     * @param int $width
     * @param int $height
     * @param int[] $aoi
     */
    public function __construct(
        $location = Text::NIL,
        $filename = Text::NIL,
        $filesize = Integer::NIL,
        $mimetype = Text::NIL,
        $source = Text::NIL,
        $title = Text::NIL,
        $caption = Text::NIL,
        $copyright = Text::NIL,
        $copyright_url = Url::NIL,
        $metadata = KeyValueList::NIL,
        $width = Integer::NIL,
        $height = Integer::NIL,
        $aoi = IntegerList::NIL
    ) {
        parent::__construct(
            $location,
            $filename,
            $filesize,
            $mimetype,
            $source,
            $title,
            $caption,
            $copyright,
            $copyright_url,
            $metadata,
            $width,
            $height,
            $aoi
        );

        $this->width = new Integer($width);
        $this->height = new Integer($height);
        $this->aoi = new IntegerList($aoi);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return array_merge(
            parent::toNative(),
            [
                'width' => $this->getWidth()->toNative(),
                'height' => $this->getHeight()->toNative(),
                'aoi' => $this->getAoi()->toNative()
            ]
        );
    }

    /**
     * @return Integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return Integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return IntegerList
     */
    public function getAoi()
    {
        return $this->aoi;
    }
}
