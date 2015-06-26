<?php

namespace Trellis\Runtime\Validator\Result;

interface IncidentInterface
{
    const SUCCESS = 0;

    const NOTICE = 3;

    const ERROR = 5;

    const CRITICAL = 7;

    public function getSeverity();

    public function getName();

    public function getParameters();

    public function getParameter($name, $default = null);

    public function hasParameter($name);
}
