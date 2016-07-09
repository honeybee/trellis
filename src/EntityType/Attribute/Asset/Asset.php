<?php

namespace Trellis\EntityType\Attribute\Asset;

use Assert\Assertion;
use Trellis\EntityType\Attribute\Integer\Integer;
use Trellis\EntityType\Attribute\KeyValueList\KeyValueList;
use Trellis\EntityType\Attribute\Text\Text;
use Trellis\EntityType\Attribute\Url\Url;
use Trellis\Entity\Value\NativeEqualsComparison;
use Trellis\Entity\Value\ValueInterface;

class Asset implements ValueInterface
{
    use NativeEqualsComparison;

    /**
     * @var Text $location
     */
    private $location;

    /**
     * @var Text $filename
     */
    private $filename;

    /**
     * @var Integer $filesize
     */
    private $filesize;

    /**
     * @var Text $mimetype
     */
    private $mimetype;

    /**
     * @var Text $title
     */
    private $title;

    /**
     * @var Text $caption
     */
    private $caption;

    /**
     * @var Text $copyright
     */
    private $copyright;

    /**
     * @var Url $copyright_url
     */
    private $copyright_url;

    /**
     * @var Text $source
     */
    private $source;

    /**
     * @var KeyValueList $metadata
     */
    private $metadata;

    /**
     * @param mixed[] $asset_data
     *
     * @return Asset
     */
    public static function fromArray(array $asset_data)
    {
        $asset_args = [];
        foreach (self::getPropertyMap() as $key => $default) {
            if (isset($asset_data[$key])) {
                $asset_args[] = $asset_data[$key];
            } else {
                $asset_args[] = $default;
            }
        }

        return new static(...$asset_args);
    }

    /**
     * @return mixed[]
     */
    public static function getPropertyMap()
    {
        return [
            'location' => Text::NIL,
            'filename' => Text::NIL,
            'filesize' => Integer::NIL,
            'mimetype' => Text::NIL,
            'source' => Text::NIL,
            'title' => Text::NIL,
            'caption' => Text::NIL,
            'copyright' => Text::NIL,
            'copyright_url' => Url::NIL,
            'metadata' => KeyValueList::NIL
        ];
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
        $metadata = KeyValueList::NIL
    ) {
        $this->location = new Text($location);
        $this->filename = new Text($filename);
        $this->filesize = new Integer($filesize);
        $this->mimetype = new Text($mimetype);
        $this->source = new Text($source);
        $this->title = new Text($title);
        $this->caption = new Text($caption);
        $this->copyright = new Text($copyright);
        $this->copyright_url = new Url($copyright_url);
        $this->metadata = new KeyValueList($metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function toNative()
    {
        return [
            'location' => $this->getLocation()->toNative(),
            'filename' => $this->getFilename()->toNative(),
            'filesize' => $this->getFilesize()->toNative(),
            'mimetype' => $this->getMimetype()->toNative(),
            'source' => $this->getSource()->toNative(),
            'title' => $this->getTitle()->toNative(),
            'caption' => $this->getCaption()->toNative(),
            'copyright' => $this->getCopyright()->toNative(),
            'copyright_url' => $this->getCopyrightUrl()->toNative(),
            'metadata' => $this->getMetadata()->toNative()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return $this->location->isEmpty();
    }

    /**
     * @return Text
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return Text
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return Integer
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * @return Text
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @return Text
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return Text
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return Text
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @return Text
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * @return Url
     */
    public function getCopyrightUrl()
    {
        return $this->copyright_url;
    }

    /**
     * @return KeyValueList
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
