<?php

namespace Trellis\Runtime\Validator\Result;

use Trellis\Common\BaseObject;
use Trellis\Runtime\Validator\Rule\RuleInterface;
use Trellis\Runtime\Validator\Rule\RuleList;

interface ResultInterface
{
    /**
     * @return BaseObject
     */
    public function getSubject();

    /**
     * @return RuleList
     */
    public function getViolatedRules();

    public function getSanitizedValue();

    public function getInputValue();

    public function getSeverity();

    public function addViolatedRule(RuleInterface $rule);
}
