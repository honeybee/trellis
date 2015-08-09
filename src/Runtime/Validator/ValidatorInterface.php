<?php

namespace Trellis\Runtime\Validator;

use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\ResultInterface;
use Trellis\Runtime\Validator\Rule\RuleList;

interface ValidatorInterface
{
    /**
     * Validates the value by delegating this to validation rules.
     *
     * @return ResultInterface
     */
    public function validate($value, EntityInterface $entity = null);

    /**
     * @return RuleList
     */
    public function getRules();

    /**
     * @return string
     */
    public function getName();
}
