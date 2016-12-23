<?php

namespace Trellis\Error;

use Assert\InvalidArgumentException;

final class AssertionFailed extends InvalidArgumentException implements ErrorInterface
{

}
