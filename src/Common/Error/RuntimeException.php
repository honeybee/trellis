<?php

namespace Trellis\Common\Error;

use Trellis\ExceptionInterface;
use RuntimeException as SplRuntimeException;

/**
 * Reflects logic errors during runtime.
 * For example non-executed (switch)cases or unexpected state transitions.
 */
class RuntimeException extends SplRuntimeException implements ExceptionInterface
{
}
