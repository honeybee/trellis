<?php

namespace Trellis\Runtime\Validator\Rule\Type;

use Trellis\Runtime\Attribute\AttributeInterface;
use Trellis\Runtime\Entity\EntityInterface;
use Trellis\Runtime\Validator\Result\IncidentInterface;
use Trellis\Runtime\Validator\Rule\Rule;
use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\EmailParser;
use Egulias\EmailValidator\EmailValidator;
use InvalidArgumentException;
use ReflectionClass;

class EmailRule extends Rule
{
    protected function execute($value, EntityInterface $entity = null)
    {
        if (!is_scalar($value) || !is_string($value)) {
            $this->throwError('invalid_type', [], IncidentInterface::CRITICAL);
            return false;
        }

        $null_value = $this->getOption(AttributeInterface::OPTION_NULL_VALUE, '');
        if ($value === $null_value) {
            $this->setSanitizedValue($null_value);
            return true;
        }

        $warnings = [];
        $reason = null;

        try {
            $parser = new EmailParser(new EmailLexer());
            $parser->parse($value);
            $warnings = $parser->getWarnings();
        } catch (InvalidArgumentException $parse_error) {
            $reason = $parse_error->getMessage();
            $this->throwError('invalid_format', [ 'reason' => $reason ], IncidentInterface::ERROR);

            return false;
        }

        if (count($warnings) > 0) {
            // @todo map warnings to errors and raise critical
            // @todo raise critical as soon as max number of warnings reached
            // @todo non-mapped warnings are raised as notice
        }

        $this->setSanitizedValue($value);

        return true;
    }
}
