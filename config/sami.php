#!/usr/bin/env php
<?php

$base_dir = dirname(__DIR__);
if (!is_dir($base_dir . DIRECTORY_SEPARATOR . 'vendor')) {
    $base_dir = dirname(dirname(dirname($base_dir)));
}
if (!is_dir($base_dir . DIRECTORY_SEPARATOR . 'vendor')) {
    throw new Exception('Unable to locate vendor directory.');
}

// autoload vendor libs
$autoload_path = [ $base_dir, 'vendor', 'autoload.php' ];
require_once implode(DIRECTORY_SEPARATOR, $autoload_path);

// return SAMI configuration for generation of API documentation
return new Sami\Sami(
    $base_dir . '/src/Trellis',
    [
        'title'                 => 'Trellis API',
        'theme'                 => 'Trellis',
        'default_opened_level'  => 2,
        'build_dir'             => __DIR__.'/../build/docs/',
        'cache_dir'             => __DIR__.'/../build/cache',
        'template_dirs'         => array(__DIR__.'/sami-theme'),
        'favicon'               => 'trellis-favicon.png'
    ]
);
