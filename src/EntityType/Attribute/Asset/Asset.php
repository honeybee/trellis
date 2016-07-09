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
     * @var string $location
     */
    private $location;

    /**
     * @var string $filename
     */
    private $filename;

    /**
     * @var int $filesize
     */
    private $filesize;

    /**
     * @var string $mimetype
     */
    private $mimetype;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var string $caption
     */
    private $caption;

    /**
     * @var string $copyright
     */
    private $copyright;

    /**
     * @var string $copyright_url
     */
    private $copyright_url;

    /**
     * @var string $source
     */
    private $source;

    /**
     * @var mixed[] $metadata
     */
    private $metadata;

    public static function fromArray(array $asset_data)
    {
        $supported_args = [
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

        $asset_args = [];
        foreach ($supported_args as $key => $default) {
            if (isset($asset_data[$key])) {
                $asset_args[] = $asset_data[$key];
            } else {
                $asset_args[] = $default;
            }
        }

        return new static(...$asset_args);
    }

    /**
     * @param string $asset
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

    public function getLocation()
    {
        return $this->location;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getFilesize()
    {
        return $this->filesize;
    }

    public function getMimetype()
    {
        return $this->mimetype;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCaption()
    {
        return $this->caption;
    }

    public function getCopyright()
    {
        return $this->copyright;
    }

    public function getCopyrightUrl()
    {
        return $this->copyright_url;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }
}
