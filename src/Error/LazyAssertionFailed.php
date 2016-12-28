<?php

namespace Trellis\Error;

use Assert\LazyAssertionException;

final class LazyAssertionFailed extends LazyAssertionException implements ErrorInterface
{
    /**
     * @var string[]
     */
    private $property_paths = [];

    /**
     * @param string $message
     * @param AssertionFailed[] $errors
     */
    public function __construct($message, array $errors)
    {
        parent::__construct($message, $errors);

        $paths = [];
        foreach ($errors as $error) {
            $paths[] = $error->getPropertyPath();
        }

        $this->property_paths = array_values(array_unique($paths));
    }

    /**
     * @return string[]
     */
    public function getPropertyPaths()
    {
        return $this->property_paths;
    }
}
