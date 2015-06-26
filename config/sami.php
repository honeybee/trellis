#!/usr/bin/env php
<?php

$baseDir = dirname(__DIR__);
if (! is_dir($baseDir . DIRECTORY_SEPARATOR . 'vendor'))
{
    $baseDir = dirname(dirname(dirname($baseDir)));
}
if (! is_dir($baseDir . DIRECTORY_SEPARATOR . 'vendor'))
{
    throw new Exception('Unable to locate vendor directory.');
}

// autoload vendor libs
$autoloadPath = array($baseDir, 'vendor', 'autoload.php');
require_once implode(DIRECTORY_SEPARATOR, $autoloadPath);

// return SAMI configuration for generation of API documentation
return new Sami\Sami($baseDir . '/src/Trellis', array(
    'title'                 => 'Trellis API',
    'theme'                 => 'Trellis',
    'default_opened_level'  => 2,
    'build_dir'             => __DIR__.'/../build/docs/',
    'cache_dir'             => __DIR__.'/../build/cache',
    'template_dirs'         => array(__DIR__.'/sami-theme'),
    'favicon'               => 'trellis-favicon.png',
//    'base_url'              => 'http://localhost:8081/docs/',
));
