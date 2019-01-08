<?php

namespace Trellis\Runtime\Validator;

use Trellis\Common\BaseObject;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Result\Result;
use Trellis\Runtime\Validator\Rule\Rule;
use Trellis\Runtime\Validator\Rule\RuleList;

class Validator extends BaseObject implements ValidatorInterface
{
    protected $name;

    protected $rules;

    public function __construct($name, RuleList $rules)
    {
        parent::__construct([]);
        $this->name = $name;
        $this->rules = $rules;
    }

    public function validate($value, EntityInterface $entity = null)
    {
        $result = new Result($this);
        $result->setInputValue($value);

        $success = true;
        foreach ($this->rules as $rule) {
            if ($rule->apply($value, $entity)) {
                $value = $rule->getSanitizedValue();
            } else {
                $success = false;
                $result->addViolatedRule($rule);
                if ($result->getSeverity() === IncidentInterface::CRITICAL) {
                    // abort validation process for critical errors
                    break;
                }
            }
        }

        if ($success) {
            $result->setSanitizedValue($value);
        }

        return $result;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRules()
    {
        return $this->rules;
    }
}
