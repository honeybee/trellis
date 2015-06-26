<?php

namespace Trellis\Common\Error;

use Trellis\ExceptionInterface;
use InvalidArgumentException;

/**
 * Reflects exceptions that occur in the context of invalid/bad values trying to be assigned somewhere.
 */
class BadValueException extends InvalidArgumentException implements ExceptionInterface
{
}
