<?php

namespace Trellis;

use Exception;

/**
 * Represent autoload errors which should most commonly come from the Autoloader.
 */
class AutoloadException extends Exception implements ExceptionInterface
{
}
