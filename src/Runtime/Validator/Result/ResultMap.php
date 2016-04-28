<?php

namespace Trellis\Runtime\Validator\Result;

use Trellis\Common\Collection\TypedMap;
use Trellis\Common\Collection\UniqueValueInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;

class ResultMap extends TypedMap implements UniqueValueInterface
{
    public function worstSeverity()
    {
        $severity = IncidentInterface::SUCCESS;
        foreach ($this->items as $result) {
            $severity = max($severity, $result->getSeverity());
        }

        return $severity;
    }

    protected function getItemImplementor()
    {
        return ResultInterface::CLASS;
    }
}
