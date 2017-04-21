<?php

namespace Trellis;

interface MapsToNativeValueInterface
{
    /**
     * @param mixed $nativeValue
     * @param mixed[] $context
     * @return mixed
     */
    public static function fromNative($nativeValue, array $context = []);

    /**
     * @return mixed
     */
    public function toNative();
}
